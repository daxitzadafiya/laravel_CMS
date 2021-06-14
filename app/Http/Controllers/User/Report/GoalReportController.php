<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ReportService;

class GoalReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke()
    {
        $company = Company::select('id', 'display_name', 'business_year_start_month', 'business_year_start_day', 'registration_date', 'deals_updated_date')->findorFail(auth()->user()->company_id);

        return $this->sendResponse($this->reportService->goalReport($company));
    }
}
