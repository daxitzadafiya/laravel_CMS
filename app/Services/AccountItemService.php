<?php

namespace App\Services;

use App\Models\AccountCategory;
use App\Models\AccountItem;

class AccountItemService
{
    public function getAccountItems($company, $request)
    {
        $accountCategoryIds = $request->input('category')
            ? $this->getAccountCategoryIds($request->input('category'))
            : [];

        $accountItems = AccountItem::where('company_id', $company->id)
            ->when($request->input('category'), function ($query) use ($accountCategoryIds) {
                $query->whereIn('account_category_id', $accountCategoryIds);
            })
            ->when($request->input('subtype'), function ($query, $subtype) {
                $query->where(function ($query) use ($subtype) {
                    $query->where('subtype', $subtype);

                    if ($subtype == 'O') {
                        $query->orWhereNull('subtype');
                    }
                });
            })->get();

        return $accountItems;
    }

    public function getMonthlyDealsByAccountItem($company, $businessYear, $dealType)
    {
        $accountItemDeals = $this->getMonthlyAccountItemDealsCollection($company, $businessYear, $dealType);
        $periodMonths = getMonthsInPeriod($businessYear['start_date'], $businessYear['end_date']);

        $accountItems = $accountItemDeals->map(function ($accountItem) use ($periodMonths, $dealType) {
            $monthlyDeals = [];

            foreach ($periodMonths as $periodMonth) {
                $monthDeal = $accountItem->deals->where('year_month', $periodMonth)->first();
                $manualJournal = $accountItem->manualJournals->where('year_month', $periodMonth)->first();
                $amount = 0;

                if ($dealType == 'income') {
                    $amount = $monthDeal
                        ? $amount + $monthDeal->credit_amount - $monthDeal->debit_amount
                        : $amount;
                    $amount = $manualJournal
                        ? $amount + $manualJournal->credit_amount - $manualJournal->debit_amount
                        : $amount;
                }

                if ($dealType == 'expense') {
                    $amount = $monthDeal
                        ? $amount + $monthDeal->debit_amount - $monthDeal->credit_amount
                        : $amount;
                    $amount = $manualJournal
                        ? $amount + $manualJournal->debit_amount - $manualJournal->credit_amount
                        : $amount;
                }

                $monthlyDeals[] = (object) [
                        'month' => $periodMonth,
                        'amount' => $amount,
                    ];
            }

            unset($accountItem->deals);
            unset($accountItem->manualJournals);
            $accountItem->monthly_deals = collect($monthlyDeals);

            return $accountItem;
        });

        return $accountItems;
    }

    protected function getMonthlyAccountItemDealsCollection($company, $businessYear, $dealType)
    {
        return AccountItem::select('id', 'name', 'type', 'subtype')
            ->with([
                'deals' => function ($query) use ($company, $businessYear) {
                    $query->select('account_item_id')
                        ->selectRaw(
                            "DATE_FORMAT(issue_date, '%Y-%c') as 'year_month',
                        SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount,
                        SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount"
                        )
                        ->where('company_id', $company->id)
                        ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
                        ->groupByRaw('account_item_id, DATE_FORMAT(issue_date, "%Y-%c")');
                },
                'manualJournals' => function ($query) use ($company, $businessYear) {
                    $query->select('account_item_id')
                        ->selectRaw(
                            "DATE_FORMAT(issue_date, '%Y-%c') as 'year_month',
                            SUM(CASE WHEN entry_side = 'credit' THEN amount ELSE 0 END) as credit_amount,
                            SUM(CASE WHEN entry_side = 'debit' THEN amount ELSE 0 END) as debit_amount"
                        )
                        ->where('company_id', $company->id)
                        ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
                        ->groupByRaw('account_item_id, DATE_FORMAT(issue_date, "%Y-%c")');
                },
            ])
            ->where(function ($query) use ($company, $businessYear) {
                $query->whereHas('deals', function ($query) use ($company, $businessYear) {
                        $query->where('company_id', $company->id)
                            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']]);
                    })
                    ->orWhereHas('manualJournals', function ($query) use ($company, $businessYear) {
                        $query->where('company_id', $company->id)
                            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']]);
                    });
            })
            ->where('type', $dealType)
            ->get();
    }

    protected function getAccountCategoryIds($parentCategoryId)
    {
        $accountCategories = AccountCategory::with('allChildren')->find($parentCategoryId);

        $accountCategories = $accountCategories
            ? $accountCategories->toArray()
            : [];

        $accountCategoryIds = [];

        array_walk_recursive($accountCategories, function ($value, $key) use (& $accountCategoryIds) {
            if ($key == 'id') {
                $accountCategoryIds[] = $value;
            }
        });

        return $accountCategoryIds;
    }
}
