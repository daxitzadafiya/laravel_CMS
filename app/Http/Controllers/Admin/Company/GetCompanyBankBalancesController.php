<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetCompanyBankBalancesRequest;
use App\Models\Company;
use App\Services\AdminReportService;
use App\Services\CompanyService;

class GetCompanyBankBalancesController extends Controller
{
    protected $companyService;
    protected $reportService;

    public function __construct(
        CompanyService $companyService,
        AdminReportService $reportService
    )
    {
        $this->companyService = $companyService;
        $this->reportService = $reportService;
    }

    public function __invoke(Company $company, GetCompanyBankBalancesRequest $request)
    {
        $businessYear = $request->input('term')
            ? $this->companyService->getBusinessYearFromTerm($company, $request->input('term'))
            : $company->current_business_year;

        return $this->sendResponse($this->reportService->companyBankBalnceReport($company, $businessYear['start_date'], $businessYear['end_date']));
    }
}
