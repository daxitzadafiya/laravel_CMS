<?php

namespace App\Services;


use App\Models\FlRatio;

class FLService
{
    protected $companyService;

    public function __construct(
        CompanyService $companyService
    )
    {
        $this->companyService = $companyService;
    }

    public function getActiveFLRatio($company, $businessYear) {
        $activeFlRatio = FlRatio::where('company_id', $company->id)
            ->where('business_year_start', $businessYear['start_date'])
            ->where('business_year_end', $businessYear['end_date'])
            ->where('status', 1)
            ->first();

        return $activeFlRatio;
    }

    public function getFLCosts($expense = [])
    {
        return [
            'total' => [
                'amount' => (int) $expense['food'] + $expense['labor'],
                'percentage' => $expense['total']
                    ? round(($expense['food'] + $expense['labor']) / $expense['total'] * 100, 1)
                    : 0,
            ],
            'f_cost' => [
                'amount' => (int) $expense['food'],
                'percentage' => $expense['total']
                    ? round($expense['food'] / $expense['total'] * 100, 1)
                    : 0,
            ],
            'l_cost' =>  [
                'amount' => (int) $expense['labor'],
                'percentage' => $expense['total']
                    ? round($expense['labor'] / $expense['total'] * 100, 1)
                    : 0,
            ],
        ];
    }

    public function getFLGoals($company, $businessYear, $totalIncome)
    {
        $activeFlRatio = $this->getActiveFLRatio($company, $businessYear);

        $flRatio = (object) [
            'f_ratio' => $activeFlRatio->f_ratio ?? 0,
            'l_ratio' => $activeFlRatio->l_ratio ?? 0,
        ];

        return [
            'total' => [
                'amount' => round($totalIncome * ($flRatio->f_ratio + $flRatio->l_ratio) / 100),
                'percentage' => round($flRatio->f_ratio + $flRatio->l_ratio, 1),

            ],
            'f_goal' => [
                'amount' => round($totalIncome * $flRatio->f_ratio / 100),
                'percentage' => round($flRatio->f_ratio, 1),
            ],
            'l_goal' => [
                'amount' => round($totalIncome * $flRatio->l_ratio / 100),
                'percentage' => round($flRatio->l_ratio, 1),
            ],
        ];
    }
}
