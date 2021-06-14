<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DailyDealReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'month' => ['required_with:year', 'numeric', 'between:1,12'],
            'year' => ['required_with:month', 'numeric', 'date_format:Y'],
        ]);

        $company = Company::select('id', 'display_name', 'business_year_start_month', 'business_year_start_day', 'registration_date', 'deals_updated_date')->findorFail(auth()->user()->company_id);

        return $this->sendResponse($this->reportService->dailyDealReport($company, $request->input('month', date('m')), $request->input('year', date('Y'))));
    }
}
