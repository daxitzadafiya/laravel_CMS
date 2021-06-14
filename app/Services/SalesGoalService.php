<?php

namespace App\Services;

class SalesGoalService
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        return $this->companyService = $companyService;
    }

    public function getGoal($company, $businessYear)
    {
        $saleGoal = $this->companyService->getActiveSalesGoal($company, $businessYear);

        if ($saleGoal && $saleGoal->values->count() == 0) {
            return (object) [
                'goal_total' => 0,
                'supposed_total' => 0,
            ];
        }

        $goalValues = $saleGoal->values ?? collect([]);

        return (object) [
            'goal_total' => $goalValues->sum('goal'),
            'supposed_total' => $businessYear == $company->current_business_year
                ? $this->getGoalTillDate($goalValues, $businessYear['start_date'], $company->deals_updated_date)
                : $goalValues->sum('goal'),
        ];
    }

    public function getGoalTillDate($monthlyGoals, $startDate, $endDate)
    {
        $monthsPeriod = getMonthsInPeriod($startDate, $endDate->copy()->subMonth(1));
        $goalTillLastMonth = $monthlyGoals->whereIn('year_month', $monthsPeriod)->sum('goal');
        $goalCurrentMonth = $monthlyGoals->where('year_month', $endDate->format('Y-n'))->first()->goal ?? 0;

        return $goalTillLastMonth + ($goalCurrentMonth / $endDate->format('t') * $endDate->format('d'));
    }
}
