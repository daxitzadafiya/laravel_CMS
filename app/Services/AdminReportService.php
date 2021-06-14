<?php

namespace App\Services;

use App\Http\Resources\AccountItemMonthlyDealResource;
use App\Http\Resources\BusinessYearResource;
use App\Models\WalletTxn;
use Carbon\Carbon;

class AdminReportService
{
    protected $companyService;
    protected $accountItemService;
    protected $goalService;
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

    public function companyDealsReport($company, $businessYear)
    {
        $incomeDeals = $this->accountItemService->getMonthlyDealsByAccountItem($company, $businessYear, 'income');
        $expenseDeals = $this->accountItemService->getMonthlyDealsByAccountItem($company, $businessYear, 'expense');
        $goal = $this->salesGoalService->getGoal($company, $businessYear);

        $totalIncome = $incomeDeals->sum(function ($incomeDeal) {
            return $incomeDeal->monthly_deals->sum('amount');
        });

        $totalExpense = $expenseDeals->sum(function ($expenseDeal) {
            return $expenseDeal->monthly_deals->sum('amount');
        });

        $foodExpense = $expenseDeals->where('subtype', 'F')->sum(function ($expenseDeal) {
            return $expenseDeal->monthly_deals->sum('amount');
        });

        $laborExpense = $expenseDeals->where('subtype', 'L')->sum(function ($expenseDeal) {
            return $expenseDeal->monthly_deals->sum('amount');
        });

        $flCost = $this->flService->getFLCosts([
            'total' => $totalExpense,
            'food' => $foodExpense,
            'labor' => $laborExpense,
        ]);

        $flGoal = $this->flService->getFLGoals($company, $businessYear, $totalIncome);

        $monthlyIncomeDeals = [];

        foreach ($incomeDeals as $incomeDeal) {
            foreach ($incomeDeal->monthly_deals as $monthlyDeal) {
                $monthlyIncomeDeals[$monthlyDeal->month] = [
                    'month' => [
                        'id' => $monthlyDeal->month,
                        'name' => Carbon::createFromFormat('Y-m-d', $monthlyDeal->month . '-01')->format('M'),
                    ],
                    'amount' => ($monthlyIncomeDeals[$monthlyDeal->month]['amount'] ?? 0) + $monthlyDeal->amount,
                ];
            }
        }

        $monthlyExpenseDeals = [];

        foreach ($expenseDeals as $expenseDeal) {
            foreach ($expenseDeal->monthly_deals as $monthlyDeal) {
                $monthlyExpenseDeals[$monthlyDeal->month] = [
                    'month' => [
                        'id' => $monthlyDeal->month,
                        'name' => Carbon::createFromFormat('Y-m-d', $monthlyDeal->month . '-01')->format('M'),
                    ],
                    'amount' => ($monthlyExpenseDeals[$monthlyDeal->month]['amount'] ?? 0) + $monthlyDeal->amount,
                ];
            }
        }

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'business_year' => new BusinessYearResource($businessYear),
            'sales' => [
                'total_amount' => $totalIncome,
                'supposed' => $goal->supposed_total
                    ? [
                        'amount' => (int) $goal->supposed_total,
                        'percentage' => round($totalIncome / $goal->supposed_total * 100, 1),
                    ]
                    : null,
                'goal' => $goal->goal_total
                    ? [
                        'amount' => (int) $goal->goal_total,
                        'percentage' => round($totalIncome / $goal->goal_total * 100, 1),
                    ]
                    : null,
                'monthly_deals' => array_values($monthlyIncomeDeals),
                'account_item_deals' => AccountItemMonthlyDealResource::collection($incomeDeals),
            ],
            'costs' => [
                'total_amount' => (int) $totalExpense,
                'fl_cost' => $flCost ?? null,
                'fl_goal' => $flGoal ?? null,
                'monthly_deals' => array_values($monthlyExpenseDeals),
                'account_item_deals' => AccountItemMonthlyDealResource::collection($expenseDeals),
            ],
        ];
    }

    public function companyBankBalnceReport($company, $startDate, $endDate)
    {
        $transactions = WalletTxn::selectRaw("date, balance, walletable_id, DATE_FORMAT(date, '%Y-%c') as 'year_month'")
            ->whereIn('id', function ($query) use ($company, $startDate, $endDate) {
                $query->selectRaw('MAX(id)')->from('wallet_txns')
                    ->where('company_id', $company->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->groupBy('walletable_id')
                    ->groupByRaw('DATE_FORMAT(date, "%Y-%c")');
            })
            ->get();

        $bankAccounts = $company->bankAccounts();

        $periodMonths = getMonthsInPeriod($company->current_business_year['start_date'], $company->deals_updated_date->format('Y-m-d'));

        $monthlyBalances = [];

        foreach ($periodMonths as $periodMonth) {
            $periodMonthSplit = explode('-', $periodMonth);
            $monthTransactions = $transactions->where('year_month', $periodMonth);

            if ($monthTransactions->isEmpty()) {
                continue;
            }

            $balance = $monthTransactions->sum('balance');

            $bankBalances = [];

            foreach ($bankAccounts as $bankAccount) {
                $bankBalances[] = [
                    'id' => $bankAccount->id,
                    'name' => $bankAccount->name,
                    'balance' => $monthTransactions->where('walletable_id', $bankAccount->id)->first()->balance ?? 0,
                ];
            }

            $monthlyBalances[] = [
                'month' => [
                    'id' => $periodMonth,
                    'name' => $periodMonthSplit[1] . '月',
                    'long_name' => $periodMonthSplit[0] . '年' . $periodMonthSplit[1] . '月',
                ],
                'balance' => (int) $balance,
                'transition' => isset($previousBalance)
                    ? (int) $balance - $previousBalance
                    : null,
                'banks' => $bankBalances,
            ];

            $previousBalance = $balance;
        }

        return [
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'monthly_balances' => array_reverse($monthlyBalances),
        ];
    }

    public function companyPerformanceReport($company, $businessYear)
    {
        $monthlySales = $this->companyService->getMonthlyDealsByAccountItemType($company, $businessYear, 'income');
        $monthlyGoals = $this->companyService->getMonthlySalesGoals($company, $businessYear);
        $monthlyCosts = $this->companyService->getMonthlyExpenseByAccountItemSubType($company, $businessYear);
        $flRatio = $this->flService->getActiveFLRatio($company, $businessYear);

        $monthlyPerformance = [];
        $totalSale = 0;
        $totalCost = 0;

        foreach ($monthlySales as $monthlySale) {
            $totalSale = $totalSale + $monthlySale->income;
            $goal = $monthlyGoals->where('year_month', $monthlySale->year_month)->first()->goal ?? 0;
            $foodCost = $monthlyCosts->where('year_month', $monthlySale->year_month)->sum('f_cost');
            $laborCost = $monthlyCosts->where('year_month', $monthlySale->year_month)->sum('l_cost');
            $otherCost = $monthlyCosts->where('year_month', $monthlySale->year_month)->sum('o_cost');
            $monthlyTotalCost = $foodCost + $laborCost + $otherCost;
            $totalCost = $totalCost + $monthlyTotalCost;

            $monthlyPerformance[] = [
                'month' => [
                    'id' => $monthlySale->year_month,
                    'name' => $monthlySale->month . '月',
                    'long_name' => $monthlySale->year . '年' . $monthlySale->month . '月',
                ],
                'sale' => (int) $monthlySale->income,
                'cost' => (int) $monthlyTotalCost,
                'goal' => (int) $goal,
                'attain_percentage' => $goal && $monthlySale->income
                    ? round($monthlySale->income / $goal * 100, 2)
                    : 0,
                'fl_cost' => [
                    'total' => [
                        'amount' => $foodCost + $laborCost,
                        'percentage' => $monthlySale->income
                            ? round(($foodCost + $laborCost) / $monthlySale->income * 100, 2)
                            : 0,
                        'goal_percentage' => $flRatio
                            ? $flRatio->f_ratio + $flRatio->l_ratio
                            : 0,
                    ],
                    'f_cost' => [
                        'amount' => (int) $foodCost,
                        'percentage' => $monthlySale->income
                            ? round($foodCost / $monthlySale->income * 100, 2)
                            : 0,
                        'goal_percentage' => $flRatio->f_ratio ?? 0,
                    ],
                    'l_cost' => [
                        'amount' => (int) $laborCost,
                        'percentage' => $monthlySale->income
                            ? round($laborCost / $monthlySale->income * 100, 2)
                            : 0,
                        'goal_percentage' => $flRatio->l_ratio ?? 0,
                    ],
                ],
            ];
        }

        return [
            'total_sale' => $totalSale,
            'total_cost' => $totalCost,
            'total_food_cost' => $monthlyCosts->sum('f_cost'),
            'total_labor_cost' => $monthlyCosts->sum('l_cost'),
            'monthly_performance' => $monthlyPerformance,
        ];
    }
}
