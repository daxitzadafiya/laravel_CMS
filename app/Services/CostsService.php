<?php

namespace App\Services;

class CostsService
{
    public function getTotalFoodLaborCosts($company, $businessYear)
    {
        $deals = $company->deals()
            ->join('account_items', function ($join) {
                $join->on('deals.account_item_id', '=', 'account_items.id')
                    ->whereIn('account_items.subtype', ['F', 'L']);
            })
            ->select('account_items.subtype', 'deals.entry_side')
            ->selectRaw("SUM(deals.amount) as amount")
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->groupBy('account_items.subtype')
            ->groupBy('deals.entry_side')
            ->get();

        $manualJournals = $company->manualJournals()
            ->join('account_items', function ($join) {
                $join->on('manual_journals.account_item_id', '=', 'account_items.id')
                    ->whereIn('account_items.subtype', ['F', 'L']);
            })
            ->select('account_items.subtype', 'manual_journals.entry_side')
            ->selectRaw("SUM(manual_journals.amount) as amount")
            ->whereBetween('issue_date', [$businessYear['start_date'], $businessYear['end_date']])
            ->groupBy('account_items.subtype')
            ->groupBy('manual_journals.entry_side')
            ->get();

        $allTransactions = $deals->concat($manualJournals);

        $foodCredit = $allTransactions->where('subtype', 'F')
            ->where('entry_side', 'credit')
            ->sum('amount');

        $foodDebit = $allTransactions->where('subtype', 'F')
            ->where('entry_side', 'debit')
            ->sum('amount');

        $laborCredit = $allTransactions->where('subtype', 'L')
            ->where('entry_side', 'credit')
            ->sum('amount');

        $laborDebit = $allTransactions->where('subtype', 'L')
            ->where('entry_side', 'debit')
            ->sum('amount');

        return (object) [
            'total' => $foodDebit - $foodCredit + $laborDebit - $laborCredit,
            'f_cost' => $foodDebit - $foodCredit,
            'l_cost' => $laborDebit - $laborCredit,
        ];
    }
}
