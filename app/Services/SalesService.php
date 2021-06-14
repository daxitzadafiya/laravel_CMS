<?php

namespace App\Services;

class SalesService
{
    public function getTotalSale($company, $businessYear)
    {
        $deals = $company->deals()
            ->join('account_items', function ($join) {
                $join->on('deals.account_item_id', '=', 'account_items.id')
                    ->where('account_items.type', 'income');
            })
            ->select('account_items.type', 'deals.entry_side')
            ->selectRaw("SUM(deals.amount) as amount")
            ->whereBetween('deals.issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->groupBy('account_items.type')
            ->groupBy('deals.entry_side')
            ->get();

        $manualJournals = $company->manualJournals()
            ->join('account_items', function ($join) {
                $join->on('manual_journals.account_item_id', '=', 'account_items.id')
                    ->where('account_items.type', 'income');
            })
            ->select('account_items.type', 'manual_journals.entry_side')
            ->selectRaw("SUM(manual_journals.amount) as amount")
            ->whereBetween('manual_journals.issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->groupBy('account_items.type')
            ->groupBy('manual_journals.entry_side')
            ->get();

        $allTransactions = $deals->concat($manualJournals);

        return $allTransactions->where('entry_side', 'credit')->sum('amount') - $allTransactions->where('entry_side', 'debit')->sum('amount');
    }
}
