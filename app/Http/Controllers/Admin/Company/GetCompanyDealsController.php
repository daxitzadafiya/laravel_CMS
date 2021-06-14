<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GetCompanyDealsRequest;
use App\Http\Resources\AccountItemDealByMonthResource;
use App\Http\Resources\AccountItemSaleByMonthResource;
use App\Http\Resources\SalesByMonthResource;
use App\Models\Company;
use App\Services\AdminReportService;
use App\Services\CompanyService;

class GetCompanyDealsController extends Controller
{
    protected $companyService;
    protected $adminReportService;

    public function __construct(
        CompanyService $companyService,
        AdminReportService $adminReportService
    ) {
        $this->companyService = $companyService;
        $this->adminReportService = $adminReportService;
    }

    public function __invoke(Company $company, GetCompanyDealsRequest $request)
    {
        $businessYear = $request->input('term')
            ? $this->companyService->getBusinessYearFromTerm($company, $request->input('term'))
            : $company->current_business_year;

        return $this->sendResponse($this->adminReportService->companyDealsReport($company, $businessYear));
    }
}
