<?php

namespace App\Services;

use App\Http\Resources\MonthGoalResource;
use App\Http\Resources\MonthlySalesFLRatioResource;
use App\Http\Resources\MonthSaleCostResource;
use App\Http\Resources\WalletableResource;
use App\Models\Deal;
use App\Models\Walletable;
use App\Models\WalletTxn;
use Carbon\CarbonPeriod;

class ReportService
{
    protected $companyService;
    protected $accountItemService;
    protected $salesGoalService;
    protected $flService;

    public function __construct(
        CompanyService $companyService,
        AccountItemService $accountItemService,
        SalesGoalService $salesGoalService,
        FLService $flService
    ) {
        $this->companyService = $companyService;
        $this->accountItemService = $accountItemService;
        $this->salesGoalService = $salesGoalService;
        $this->flService = $flService;
    }

    public function topPageReport($company)
    {
        $currentMonth = $company->deals_updated_date->format('Y-n');
        $currentMonthDays = $company->deals_updated_date->format('t');
        $dayToday = $company->deals_updated_date->format('j');

        $monthlyDeals = $this->companyService->getMonthlyDealsByAccountItemType($company, ['start_date' => $company->current_business_year['start_date'], 'end_date' => $company->deals_updated_date->format('Y-m-d')]);
        $goals = $this->companyService->getMonthlySalesGoals($company, $company->current_business_year);

        foreach ($goals as $goal) {
            $goal->sale = $monthlyDeals->where('year_month', $goal->year_month)->first()->income ?? 0;
        }

        $monthIncomeTillDate = $monthlyDeals->where('year_month', $currentMonth)->first()->income ?? 0;
        $goalMonth = $goals->where('year_month', $currentMonth)->first()->goal;
        $monthGoalTillDate = $goalMonth / $currentMonthDays * $dayToday;

        $lastMonthExpense = $this->companyService->getMonthlyExpenseByAccountItemSubType($company, ['start_date' => date('Y-m-d', strtotime('first day of last month')), 'end_date' => date('Y-m-d', strtotime('last day of last month'))])->first();

        $lastMonthSale = Deal::where('company_id', $company->id)
            ->where('type', 'income')
            ->whereDate('issue_date', '>=', date('Y-m-d', strtotime('first day of last month')))
            ->whereDate('issue_date', '<=', date('Y-m-d', strtotime('last day of last month')))
            ->sum('amount');

        $walletables = Walletable::where('company_id', $company->id)
            ->where('type', 'bank_account')
            ->where('last_balance', '>', 0)
            ->get();

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'month' => [
                'id' => $currentMonth,
                'name' => $company->deals_updated_date->format('n') . '月',
                'sale_to_date' => (int) $monthIncomeTillDate ?? 0,
                'goal_to_date' => (int) $monthGoalTillDate ?? 0,
                'goal' => (int) $goalMonth ?? 0,
                'success_percentage' => $monthGoalTillDate
                    ? round($monthIncomeTillDate / $monthGoalTillDate * 100, 1)
                    : 0,
                'goal_progress_percentage' => $goalMonth
                    ? round($monthGoalTillDate / $goalMonth * 100, 1)
                    : 0,
                'actual_progress_percentage' => $goalMonth
                    ? round($monthIncomeTillDate / $goalMonth * 100, 1)
                    : 0,
                'fl_costs' => [
                    'date' => date('Y-m-d', strtotime('last day of last month')),
                    'total' => [
                        'amount' => $lastMonthExpense->f_cost + $lastMonthExpense->l_cost,
                        'percentage' => $lastMonthSale
                            ? round(($lastMonthExpense->f_cost + $lastMonthExpense->l_cost) / $lastMonthSale * 100, 1)
                            : 0,
                    ],
                    'f_cost' => [
                        'amount' => (int) $lastMonthExpense->f_cost,
                        'percentage' => $lastMonthSale
                            ? round($lastMonthExpense->f_cost / $lastMonthSale * 100, 1)
                            : 0,
                    ],
                    'l_cost' => [
                        'amount' => (int) $lastMonthExpense->l_cost,
                        'percentage' => $lastMonthSale
                            ? round($lastMonthExpense->l_cost / $lastMonthSale * 100, 1)
                            : 0,
                    ],
                ],
                'other_costs' => [
                    'amount' => (int) $lastMonthExpense->o_cost,
                    'percentage' => $lastMonthSale
                        ? round($lastMonthExpense->o_cost / $lastMonthSale * 100, 1)
                        : 0,
                ],
            ],
            'bank_balance' => [
                'total' => $walletables->sum('last_balance'),
                'banks' => WalletableResource::collection($walletables),
            ],
            'max_month_sale' => (int) $monthlyDeals->max('income'),
            'max_month_cost' => (int) $monthlyDeals->max('expense'),
            'monthly_deals' => MonthSaleCostResource::collection($monthlyDeals),
        ];
    }

    public function goalReport($company)
    {
        $currentMonth = $company->deals_updated_date->format('Y-n');
        $currentMonthDays = $company->deals_updated_date->format('t');
        $dayToday = $company->deals_updated_date->format('j');

        $monthlyDeals = $this->companyService
            ->getMonthlyDealsByAccountItemType($company, $company->current_business_year, 'income');

        $goals = $this->companyService
            ->getMonthlySalesGoals($company, $company->current_business_year);

        foreach ($goals as $goal) {
            $goal->sale = $monthlyDeals->where('year_month', $goal->year_month)->first()->income ?? 0;
        }

        $monthIncomeTillDate = $monthlyDeals->where('year_month', $currentMonth)->first()->income ?? 0;
        $goalMonth = $goals->where('year_month', $currentMonth)->first()->goal;
        $monthGoalTillDate = $goalMonth / $currentMonthDays * $dayToday;

        $yearIncomeTillDate = $monthlyDeals->sum('income');
        $yearGoalTillDate = $this->salesGoalService->getGoalTillDate($goals, $company->current_business_year['start_date'], $company->deals_updated_date) ?? 0;

        return  [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'company' => [
                'id' => $company->id,
                'display_name' => $company->display_name,
            ],
            'month' => [
                'id' => $currentMonth,
                'name' => $company->deals_updated_date->format('n') . '月',
                'sale_to_date' => (int) $monthIncomeTillDate ?? 0,
                'goal_to_date' => (int) $monthGoalTillDate ?? 0,
                'goal' => (int) $goalMonth ?? 0,
                'success_percentage' => $monthGoalTillDate
                    ? round($monthIncomeTillDate / $monthGoalTillDate * 100, 1)
                    : 0,
                'goal_progress_percentage' => $goalMonth
                    ? round($monthGoalTillDate / $goalMonth * 100, 1)
                    : 0,
                'actual_progress_percentage' => $goalMonth
                    ? round($monthIncomeTillDate / $goalMonth * 100, 1)
                    : 0,
            ],
            'year' => [
                'term' => $company->current_business_year['term'],
                'start_date' => $company->current_business_year['start_date']->format('Y-m-d'),
                'end_date' => $company->current_business_year['end_date']->format('Y-m-d'),
                'sale_to_date' => (int) $yearIncomeTillDate,
                'goal_to_date' => (int) $yearGoalTillDate,
                'success_percentage' => $yearGoalTillDate
                    ? round($yearIncomeTillDate / $yearGoalTillDate * 100, 1)
                    : 0,
                'goal' => $goals->sum('goal'),
                'monthly_goals' => MonthGoalResource::collection($goals),
            ]
        ];
    }

    public function flRatioReport($company)
    {
        $monthlyCosts = $this->companyService->getMonthlyExpenseByAccountItemSubType($company, $company->current_business_year);
        $monthlySales = $this->companyService->getMonthlyDealsByAccountItemType($company, $company->current_business_year, 'income');

        $monthlyCosts->map(function ($monthlyCost) use ($monthlySales) {
            $monthlyCost->sale = $monthlySales->where('year_month', $monthlyCost->year_month)->first()->income;
        });

        $yearFoodExpense = $monthlyCosts->sum('f_cost');
        $yearLaborExpense = $monthlyCosts->sum('l_cost');
        $yearTotalSale = $monthlySales->sum('income');

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'year' => [
                'term' => $company->current_business_year['term'],
                'start_date' => $company->current_business_year['start_date']->format('Y-m-d'),
                'end_date' => $company->current_business_year['end_date']->format('Y-m-d'),
                'fl_goal' => $this->flService->getFLGoals($company, $company->current_business_year, $yearTotalSale),
                'fl_cost' => [
                    'total' => [
                        'amount' => $yearTotalSale,
                        'percentage' => $yearTotalSale
                            ? round(($yearFoodExpense + $yearLaborExpense) / $yearTotalSale * 100, 1)
                            : 0,
                    ],
                    'f_cost' => [
                        'amount' => (int) $yearFoodExpense,
                        'percentage' => $yearTotalSale
                            ? round($yearFoodExpense / $yearTotalSale * 100, 1)
                            : 0,
                    ],
                    'l_cost' => [
                        'amount' => (int) $yearLaborExpense,
                        'percentage' => $yearTotalSale
                            ? round($yearLaborExpense / $yearTotalSale * 100, 1)
                            : 0,
                    ],
                ],
                'other_cost' => [
                    'amount' => (int) $yearTotalSale - $yearFoodExpense - $yearLaborExpense,
                    'percentage' => $yearTotalSale
                        ? round(($yearTotalSale - $yearFoodExpense - $yearLaborExpense) / $yearTotalSale * 100, 1)
                        : 0,
                ],
                'monthly_costs' => MonthlySalesFLRatioResource::collection($monthlyCosts),
            ],
        ];
    }

    public function bankTransactionReport($company, $walletable, $request)
    {
        $transactions = WalletTxn::where('walletable_id', $walletable->id)
            ->where('company_id', $company->id)
            ->when($request->input('date_from'), function ($query, $dateFrom) {
                return $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                return $query->whereDate('date', '<=', $dateTo);
            })
            ->orderBy('id', 'desc');

        return isPaginate($request->input('paginate'))
            ? $transactions->paginate($request->input('paginate', 25))
            : $transactions->get();
    }

    public function monthlyDealReport($company)
    {
        $incomeDeals = $this->accountItemService->getMonthlyDealsByAccountItem($company, $company->current_business_year, 'income');
        $expenseDeals = $this->accountItemService->getMonthlyDealsByAccountItem($company, $company->current_business_year, 'expense');

        $periodMonths = getMonthsInPeriod($company->current_business_year['start_date'], $company->deals_updated_date->format('Y-m-d'));

        $monthlyDeals = [];

        foreach ($periodMonths as $periodMonth) {
            $periodMonthSplit = explode('-', $periodMonth);

            if ($periodMonthSplit[0] . $periodMonthSplit[1] > date('Yn')) {
                continue;
            }

            $saleItems = $incomeDeals->map(function ($incomeDeal) use ($periodMonth) {
                    return [
                        'name' => $incomeDeal->name,
                        'amount' => $incomeDeal->monthly_deals->where('month', $periodMonth)->first()->amount ?? 0,
                        'subtype' => $incomeDeal->subtype,
                    ];
                })->where('amount', '!=', 0)->values();

            $costItems = $expenseDeals->map(function ($expenseDeal) use ($periodMonth) {
                    return [
                        'name' => $expenseDeal->name,
                        'amount' => $expenseDeal->monthly_deals->where('month', $periodMonth)->first()->amount ?? 0,
                        'subtype' => $expenseDeal->subtype,
                    ];
                })->where('amount', '!=', 0)->values();

            $monthlyDeals[] = [
                'month' => [
                    'id' => $periodMonth,
                    'name' => $periodMonthSplit[1] . '月',
                    'long_name' => $periodMonthSplit[0] . '年' . $periodMonthSplit[1] . '月',
                ],
                'sale' => $saleItems->sum('amount'),
                'cost' => $costItems->sum('amount'),
                'sale_deals' => $saleItems,
                'cost_deals' => $costItems,
            ];
        }

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'deals' => array_reverse($monthlyDeals),
        ];
    }

    public function dailyDealReport($company, $month, $year)
    {
        $deals = Deal::query()
            ->join('account_items', 'deals.account_item_id', '=', 'account_items.id')
            ->selectRaw('issue_date, SUM(amount) as amount, account_items.type')
            ->where(function ($querey) {
                $querey->where('account_items.type', 'income')
                    ->orWhere('account_items.type', 'expense');
            })
            ->where('deals.company_id', $company->id)
            ->whereYear('deals.issue_date', $year)
            ->whereMonth('deals.issue_date', $month)
            ->groupBy('deals.issue_date', 'account_items.type')
            ->orderBy('deals.issue_date')
            ->get();

        $dates = CarbonPeriod::create('first day of ' . $year . '-' . $month, '1 day', 'last day of ' . $year . '-' . $month);
        $dailyDeals = [];

        foreach ($dates as $date) {
            $date = $date->format('Y-m-d');
            $income = $deals->where('type', 'income')->where('issue_date', $date)->first()->amount ?? 0;
            $expense = $deals->where('type', 'expense')->where('issue_date', $date)->first()->amount ?? 0;

            if ($income == 0 && $expense == 0) {
                continue;
            }

            $dailyDeals[] = [
                'date' => $date,
                'sale' => (int) $income,
                'cost' => (int) $expense,
            ];
        }

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'deals' => $dailyDeals,
        ];
    }
}
