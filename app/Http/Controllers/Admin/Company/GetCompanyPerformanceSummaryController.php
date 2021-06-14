<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetCompanyPerformanceRequest;
use App\Http\Resources\BusinessYearResource;
use App\Models\Company;
use App\Models\Deal;
use App\Services\CompanyService;
use App\Services\FLService;
use App\Services\SalesGoalService;
use App\Services\ReportService;

class GetCompanyPerformanceSummaryController extends Controller
{
    protected $companyService;
    protected $flService;
    protected $goalService;
    protected $reportService;

    public function __construct(
        CompanyService $companyService,
        FLService $flService,
        SalesGoalService $salesGoalService,
        ReportService $reportService
    ) {
        $this->companyService = $companyService;
        $this->flService = $flService;
        $this->salesGoalService = $salesGoalService;
        $this->reportService = $reportService;
    }

    public function __invoke(Company $company, GetCompanyPerformanceRequest $request)
    {
        $businessYear = $request->input('term')
            ? $this->companyService->getBusinessYearFromTerm($company, $request->input('term'))
            : $company->current_business_year;

        $deals = Deal::selectRaw(
                "deal_details.type, SUM(amount) as amount,
                        CASE
                            WHEN account_items.subtype IS NULL THEN 'O'
                            ELSE account_items.subtype
                        END as account_item_subtype"
            )
            ->join('account_items', 'deal_details.account_item_id', '=', 'account_items.id')
            ->where('deal_details.company_id', $company->id)
            ->groupBy('deal_details.type')
            //->whereDate('issue_date', '>=', date('Y-m-d', strtotime('first day of last month')))
            //->whereDate('issue_date', '<=', date('Y-m-d', strtotime('last day of last month')))
            ->groupBy('account_item_subtype')
            ->get();

        $totalIncome = $deals->where('type', 'income')->sum('amount');
        $totalExpense = $deals->where('type', 'expense')->sum('amount');
        $foodExpense = $deals->where('type', 'expense')->where('account_item_subtype', 'F')->sum('amount');
        $laborExpense = $deals->where('type', 'expense')->where('account_item_subtype', 'L')->sum('amount');

        $flRatio = $this->flService->getActiveFLRatio($company, $company->current_business_year);
        $fRatio = $flRatio->f_ratio ?? 0;
        $lRatio = $flRatio->l_ratio ?? 0;

        return $this->sendResponse([
            'date' => $company->deals_updated_date->format('Y-m-d'),
            'business_year' => new BusinessYearResource($businessYear),
            'sales' => [
                'total_amount' => $totalIncome,
                'fl' => [
                    'f' => [
                        'amount' => round($totalIncome / 100 * $fRatio, 1),
                        'percentage' => $fRatio,
                    ],
                    'l' => [
                        'amount' => round($totalIncome / 100 * $lRatio, 1),
                        'percentage' => $lRatio,
                    ],
                ],
            ],
            'costs' => [
                'total_amount' => $totalExpense,
                'fl' => [
                    'f' => [
                        'amount' => $foodExpense,
                        'percentage' => $totalExpense
                            ? round($foodExpense / $totalExpense * 100, 1)
                            : 0,
                    ],
                    'l' => [
                        'amount' => $laborExpense,
                        'percentage' => $totalExpense
                            ? round($laborExpense / $totalExpense * 100, 1)
                            : 0,
                    ],
                ],
            ],
        ]);
    }
}
