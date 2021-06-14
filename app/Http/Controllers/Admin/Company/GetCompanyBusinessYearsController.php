<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessYearResource;
use App\Models\Company;
use App\Services\CompanyService;

class GetCompanyBusinessYearsController extends Controller
{
    public function __invoke(Company $company, CompanyService $companyService)
    {
        $businessYears = $companyService->getBusinessYears($company);

        return $this->sendResponse([
            'business_years' => BusinessYearResource::collection($businessYears),
        ]);
    }
}
