<?php

namespace App\Services;

use App\Models\AccountItem;
use App\Models\Company;
use App\Models\Deal;
use App\Models\ManualJournal;
use App\Models\SalesGoal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    public function getCompanies($request)
    {
        $companies = Company::query()
            ->with('subscriptions', 'prefecture', 'headCount')
            ->withCount('users')
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');

                switch ($sort) {
                    case 'subscription': return $query->orderBySubscriptionPlan($order);
                    case 'head_count': return $query->orderByHeadCount($order);
                    default: return $query->orderBy($sort, $order);
                }
            })
            ->when($request->input('type'), function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when(! is_null($request->input('status')), function ($query) use ($request) {
                return $query->where('status', $request->input('status'));
            })
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('postcode', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('prefecture', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subscriptions', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->where('status', 'active');
                        })
                        ->orWhereHas('headCount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            });

        return isPaginate($request->input('paginate'))
            ? $companies->paginate($request->input('paginate', 25))
            : $companies->get();
    }

    public function getActiveSalesGoal($company, $businessYear)
    {
        return SalesGoal::with(['values' => function ($query) {
                $query->selectRaw("sales_goal_id, year, month, CONCAT(year, '-', month) as 'year_month', goal");
            }])
            ->where('company_id', $company->id)
            ->where('business_year_start', $businessYear['start_date'])
            ->where('business_year_end', $businessYear['end_date'])
            ->where('status', 1)
            ->first();
    }

    public function getMonthlySalesGoals($company, $businessYear)
    {
        $saleGoal = $this->getActiveSalesGoal($company, $businessYear);
        $periodMonths = getMonthsInPeriod($businessYear['start_date'], $businessYear['end_date']);
        $response = [];

        foreach ($periodMonths as $periodMonth) {
            $periodMonthSplit = explode('-', $periodMonth);
            $goal = isset($saleGoal->values)
                ? $saleGoal->values->where('year', $periodMonthSplit[0])->where('month', $periodMonthSplit[1])->first()->goal ?? 0
                : 0;

            $response[] = (object) [
                'year_month' => $periodMonthSplit[0] . '-' . $periodMonthSplit[1],
                'year' => $periodMonthSplit[0],
                'month' => $periodMonthSplit[1],
                'goal' =>  $goal,
            ];
        }

        return collect($response);
    }

    public function getMonthlyDealsByAccountItemType($company, $businessYear, $dealType = 'all')
    {
        $deals = Deal::query()
            ->join('account_items', 'deals.account_item_id', '=', 'account_items.id')
            ->select('account_items.type')
            ->selectRaw(
                "DATE_FORMAT(issue_date, '%Y-%c') as 'year_month',
                SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount,
                SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount"
            )
            ->where('deals.company_id', $company->id)
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->when($dealType != 'all', function ($query) use ($dealType) {
                $query->where('account_items.type', $dealType);
            })
            ->groupBy('account_items.type')
            ->groupByRaw('DATE_FORMAT(issue_date, "%Y-%c")')
            ->get();

        $manualJournals = ManualJournal::query()
            ->join('account_items', 'manual_journals.account_item_id', '=', 'account_items.id')
            ->selectRaw("DATE_FORMAT(issue_date, '%Y-%c') as 'year_month', account_items.type")
            ->selectRaw("SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount")
            ->selectRaw("SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount")
            ->where('manual_journals.company_id', $company->id)
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->when($dealType != 'all', function ($query) use ($dealType) {
                $query->where('account_items.type', $dealType);
            })
            ->groupBy('account_items.type')
            ->groupByRaw('DATE_FORMAT(issue_date, "%Y-%c")')
            ->get();

        $periodMonths = getMonthsInPeriod($businessYear['start_date'], $businessYear['end_date']);

        $response = [];

        foreach ($periodMonths as $periodMonth) {
            $periodMonthSplit = explode('-', $periodMonth);

            if ($periodMonthSplit[0].$periodMonthSplit[1] > date('Yn')) {
                continue;
            }

            $monthDeals = $deals->where('year_month', $periodMonth);
            $incomeDeal = $monthDeals->where('type', 'income')->first();
            $expenseDeal = $monthDeals->where('type', 'expense')->first();

            $incomeAmount = $incomeDeal
                ? $incomeDeal->credit_amount - $incomeDeal->debit_amount
                : 0;

            $expenseAmount = $expenseDeal
                ? $expenseDeal->debit_amount - $expenseDeal->credit_amount
                : 0;

            $monthManualJournals = $manualJournals->where('year_month', $periodMonth);
            $incomeManualJournal = $monthManualJournals->where('type', 'income')->first();
            $expenseManualJournal = $monthManualJournals->where('type', 'expense')->first();

            $incomeAmount = $incomeManualJournal
                ? $incomeAmount + $incomeManualJournal->credit_amount - $incomeManualJournal->debit_amount
                : $incomeAmount;

            $expenseAmount = $expenseManualJournal
                ? $expenseAmount + $expenseManualJournal->debit_amount - $expenseManualJournal->credit_amount
                : $expenseAmount;

            if ($dealType == 'all') {
                $response[] = (object) [
                    'year_month' => $periodMonth,
                    'year' => $periodMonthSplit[0],
                    'month' => $periodMonthSplit[1],
                    'income' => $incomeAmount,
                    'expense' => $expenseAmount,
                ];
            } else {
                $response[] = (object) [
                    'year_month' => $periodMonth,
                    'year' => $periodMonthSplit[0],
                    'month' => $periodMonthSplit[1],
                    $dealType => ${$dealType . 'Amount'},
                ];
            }
        }

        return collect($response);
    }

    public function getMonthlyExpenseByAccountItemSubType($company, $businessYear, $accountItemSubtype = 'all')
    {
        $deals = Deal::query()
            ->join('account_items', 'deals.account_item_id', '=', 'account_items.id')
            ->selectRaw(
                "DATE_FORMAT(issue_date, '%Y-%c') as 'year_month',
                SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount,
                SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount,
                CASE
                    WHEN account_items.subtype IS NULL THEN 'O'
                    ELSE account_items.subtype
                END as account_item_subtype"
            )
            ->where('deals.company_id', $company->id)
            ->where('account_items.type', 'expense')
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->when($accountItemSubtype != 'all', function ($query) use ($accountItemSubtype) {
                $query->where('account_items.subtype', $accountItemSubtype);
            })
            ->groupByRaw('DATE_FORMAT(issue_date, "%Y-%c"), account_item_subtype')
            ->get();

        $manualJournals = ManualJournal::query()
            ->join('account_items', 'manual_journals.account_item_id', '=', 'account_items.id')
            ->selectRaw(
                "DATE_FORMAT(issue_date, '%Y-%c') as 'year_month',
                SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount,
                SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount,
                CASE
                    WHEN account_items.subtype IS NULL THEN 'O'
                    ELSE account_items.subtype
                END as account_item_subtype"
            )
            ->where('manual_journals.company_id', $company->id)
            ->where('account_items.type', 'expense')
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->when($accountItemSubtype != 'all', function ($query) use ($accountItemSubtype) {
                $query->where('account_items.subtype', $accountItemSubtype);
            })
            ->groupByRaw('DATE_FORMAT(issue_date, "%Y-%c"), account_item_subtype')
            ->get();

        $periodMonths = getMonthsInPeriod($businessYear['start_date'], $businessYear['end_date']);

        $response = [];

        foreach ($periodMonths as $periodMonth) {
            $periodMonthSplit = explode('-', $periodMonth);

            if ($periodMonthSplit[0].$periodMonthSplit[1] > date('Yn')) {
                continue;
            }

            $monthDeals = $deals->where('year_month', $periodMonth);
            $fDeal = $monthDeals->where('account_item_subtype', 'F')->first();
            $lDeal = $monthDeals->where('account_item_subtype', 'L')->first();
            $oDeal = $monthDeals->where('account_item_subtype', 'O')->first();

            $fCost = $fDeal ? $fDeal->debit_amount - $fDeal->credit_amount : 0;
            $lCost = $lDeal ? $lDeal->debit_amount - $lDeal->credit_amount : 0;
            $oCost = $oDeal ? $oDeal->debit_amount - $oDeal->credit_amount : 0;

            $monthManualJournals = $manualJournals->where('year_month', $periodMonth);
            $fManualJournal = $monthManualJournals->where('account_item_subtype', 'F')->first();
            $lManualJournal = $monthManualJournals->where('account_item_subtype', 'L')->first();
            $oManualJournal = $monthManualJournals->where('account_item_subtype', 'O')->first();

            $fCost = $fManualJournal ? $fCost + $fManualJournal->debit_amount - $fManualJournal->credit_amount : $fCost;
            $lCost = $lManualJournal ? $lCost + $lManualJournal->debit_amount - $lManualJournal->credit_amount : $lCost;
            $oCost = $oManualJournal ? $oCost + $oManualJournal->debit_amount - $oManualJournal->credit_amount : $oCost;

            if ($accountItemSubtype == 'all') {
                $response[] = (object) [
                    'year_month' => $periodMonth,
                    'year' => $periodMonthSplit[0],
                    'month' => $periodMonthSplit[1],
                    'f_cost' => $fCost,
                    'l_cost' => $lCost,
                    'o_cost' => $oCost,
                ];
            } else {
                $response[] = (object) [
                    'year_month' => $periodMonth,
                    'year' => $periodMonthSplit[0],
                    'month' => $periodMonthSplit[1],
                    strtolower($accountItemSubtype) . '_cost' => ${strtolower($accountItemSubtype) . 'Cost'},
                ];
            }
        }

        return collect($response);
    }

    public function getBusinessYears($company)
    {
        $termStartDate = Carbon::create($company->registration_date->format('Y'), $company->business_year_start_month, $company->business_year_start_day);

        if ($termStartDate->gt($company->registration_date))
        {
            $termStartDate->subYear(1);
        }

        $businessYears = [];
        $term = 1;

        while ($termStartDate->lt(Carbon::now())) {
            $businessYears[] = [
                'term' => $term++,
                'start_date' => $termStartDate,
                'end_date' => $termStartDate->copy()->addYear(1)->subDay(1),
            ];

            $termStartDate = $termStartDate->copy()->addYear(1);
        }

        return array_reverse($businessYears);
    }

    public function getBusinessYearFromTerm($company, $term)
    {
        if ($company->status != 1) {
            return false;
        }

        $businessYears = $this->getBusinessYears($company);

        $termYear = Arr::where($businessYears, function ($businessYear) use ($term) {
            return $businessYear['term'] == $term;
        });

        return Arr::first($termYear);
    }
}
