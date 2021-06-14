<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FLRatioRequest;
use App\Http\Resources\FLRatioResource;
use App\Models\Company;
use App\Models\FlRatio;
use App\Services\CompanyService;
use App\Services\FLService;
use Illuminate\Support\Facades\DB;

class FLRatioController extends Controller
{
    protected $companyService;
    protected $flService;

    public function __construct(
        CompanyService $companyService,
        FLService $flService
    ) {
        $this->companyService = $companyService;
        $this->flService = $flService;
    }

    public function index()
    {
        $company = Company::select('id', 'business_year_start_month', 'business_year_start_day', 'registration_date')->findorFail(auth()->user()->company_id);

        return $this->sendResponse([
            'fl_ratio' => new FLRatioResource($this->flService->getActiveFLRatio($company, $company->current_business_year))
        ]);
    }

    public function update(FLRatioRequest $request)
    {
        $data = $request->validated();

        if ($data['f_ratio'] + $data['l_ratio'] > 100) {
            return $this->sendFail('The given data was invalid.', [
                'f_ratio' => __('Total ratio should not exceeds 100%.'),
            ], 422);
        }

        $company = Company::select('id', 'business_year_start_month', 'business_year_start_day', 'registration_date')->findorFail(auth()->user()->company_id);

        $activeFlRatio = $this->flService->getActiveFLRatio($company, $company->current_business_year);

        if ($activeFlRatio && $company->current_business_year
            && $activeFlRatio->business_year_start == $company->current_business_year['start_date']
            && $activeFlRatio->business_year_end == $company->current_business_year['end_date']
            && $activeFlRatio->f_ratio == $data['f_ratio']
            && $activeFlRatio->l_ratio == $data['l_ratio']
        ) {
            return $this->sendResponse([
                'message' => __('FL ratio not changed.'),
            ]);
        }

        DB::transaction(function () use ($company, $data) {
            $company->flRatios()->update(['status' => 0]);

            FlRatio::create([
                'company_id' => $company->id,
                'business_year_start' => $company->current_business_year['start_date'],
                'business_year_end' => $company->current_business_year['end_date'],
                'f_ratio' => $data['f_ratio'],
                'l_ratio' => $data['l_ratio'],
                'user_id' => auth()->user()->id,
                'status' => 1,
            ]);
        });

        return $this->sendResponse([
            'message' => __('FL ratio updated successfully.'),
        ]);
    }
}
