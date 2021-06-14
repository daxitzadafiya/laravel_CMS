<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetCompanyPerformanceRequest;
use App\Http\Resources\BusinessYearResource;
use App\Models\Company;
use App\Services\AdminReportService;
use App\Services\CompanyService;
use App\Services\FLService;
use App\Services\SalesGoalService;
use App\Services\ReportService;

class GetCompanyPerformanceController extends Controller
{
    protected $companyService;
    protected $flService;
    protected $goalService;
    protected $adminReportService;

    public function __construct(
        CompanyService $companyService,
        FLService $flService,
        SalesGoalService $salesGoalService,
        AdminReportService $adminReportService
    ) {
        $this->companyService = $companyService;
        $this->flService = $flService;
        $this->salesGoalService = $salesGoalService;
        $this->adminReportService = $adminReportService;
    }

    public function __invoke(Company $company, GetCompanyPerformanceRequest $request)
    {
        $businessYear = $request->input('term')
            ? $this->companyService->getBusinessYearFromTerm($company, $request->input('term'))
            : $company->current_business_year;

        $monthlyPerformance = $this->adminReportService->companyPerformanceReport($company, $businessYear);

        $goal = $this->salesGoalService->getGoal($company, $businessYear);

        $flRatio = $this->flService->getActiveFLRatio($company, $company->current_business_year);
        $fRatio = $flRatio->f_ratio ?? 0;
        $lRatio = $flRatio->l_ratio ?? 0;

        return $this->sendResponse([
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'business_year' => new BusinessYearResource($businessYear),
            'sales' => [
                'total_amount' => (int) $monthlyPerformance['total_sale'],
                'supposed' => [
                    'amount' => (int) $goal->supposed_total,
                    'percentage' => $goal->supposed_total
                        ? round($monthlyPerformance['total_sale'] / $goal->supposed_total * 100, 1)
                        : 0,
                ],
                'goal' => [
                    'amount' => (int) $goal->goal_total,
                    'percentage' => $goal->goal_total
                        ? round($monthlyPerformance['total_sale'] / $goal->goal_total * 100, 1)
                        : 0,
                ],
                'fl_sale' => [
                    'total' => [
                        'amount' => round($monthlyPerformance['total_sale'] / 100 * ($fRatio + $lRatio), 1),
                        'percentage' => $fRatio + $lRatio,
                    ],
                    'f_sale' => [
                        'amount' => round($monthlyPerformance['total_sale'] * $fRatio / 100, 1),
                        'percentage' => $fRatio,
                    ],
                    'l_sale' => [
                        'amount' => round($monthlyPerformance['total_sale'] / 100 * $lRatio, 1),
                        'percentage' => $lRatio,
                    ],
                ],
            ],
            'costs' => [
                'total_amount' => (int) $monthlyPerformance['total_cost'],
                'fl_cost' => [
                    'total' => [
                        'amount' => $monthlyPerformance['total_food_cost'] + $monthlyPerformance['total_labor_cost'],
                        'percentage' => $monthlyPerformance['total_cost']
                            ? round(($monthlyPerformance['total_food_cost'] + $monthlyPerformance['total_labor_cost']) / $monthlyPerformance['total_cost'] * 100, 1)
                            : 0,
                    ],
                    'f_cost' => [
                        'amount' => (int) $monthlyPerformance['total_food_cost'],
                        'percentage' => $monthlyPerformance['total_cost']
                            ? round($monthlyPerformance['total_food_cost'] / $monthlyPerformance['total_cost'] * 100, 1)
                            : 0,
                    ],
                    'l_cost' => [
                        'amount' => $monthlyPerformance['total_labor_cost'],
                        'percentage' => $monthlyPerformance['total_cost']
                            ? round($monthlyPerformance['total_labor_cost'] / $monthlyPerformance['total_cost'] * 100, 1)
                            : 0,
                    ],
                ],
            ],
            'monthly_performance' => $monthlyPerformance['monthly_performance'],
        ]);
    }
}
