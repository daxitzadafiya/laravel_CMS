<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FLRatioRequest;
use App\Http\Resources\FLRatioResource;
use App\Models\Company;
use App\Models\FlRatio;
use App\Services\CompanyService;
use App\Services\CostsService;
use App\Services\SalesService;

class CompanyFLRatioController extends Controller
{
    protected $companyService;
    protected $salesService;
    protected $costsService;

    public function __construct(
        CompanyService $companyService,
        SalesService $salesService,
        CostsService $costsService
    ) {
        $this->companyService = $companyService;
        $this->salesService = $salesService;
        $this->costsService = $costsService;
    }

    public function index(Company $company, FLRatioRequest $request)
    {
        $businessYear = $request->getBusinessYear();

        $flRatios = FlRatio::with('user')
            ->where('company_id', $company->id)
            ->where('business_year_start', $businessYear['start_date'])
            ->where('business_year_end', $businessYear['end_date'])
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                return $query->orderBy($sort, $request->input('order'));
            })
            ->get();

        $totalSale = $this->salesService->getTotalSale($company, $businessYear);
        $flCosts = $this->costsService->getTotalFoodLaborCosts($company, $businessYear);

        return $this->sendResponse([
            'business_year' => [
                'term' => $businessYear['term'],
                'start_date' => $businessYear['start_date']->format('Y-m-d'),
                'end_date' => $businessYear['end_date']->format('Y-m-d'),
            ],
            'fl_costs' => [
                'total' => [
                    'amount' => $flCosts->total,
                    'percentage' => $totalSale
                        ? round($flCosts->total / $totalSale * 100, 1)
                        : 0,
                ],
                'f_cost' => [
                    'amount' => (int) $flCosts->f_cost,
                    'percentage' => $totalSale
                        ? round($flCosts->f_cost / $totalSale * 100, 1)
                        : 0,
                ],
                'l_cost' => [
                    'amount' => (int) $flCosts->l_cost,
                    'percentage' => $totalSale
                        ? round($flCosts->l_cost / $totalSale * 100, 1)
                        : 0,
                ],
            ],
            'fl_ratios' => FLRatioResource::collection($flRatios),
        ]);
    }

    public function show(Company $company)
    {

    }

    public function store(Company $company)
    {

    }

    public function destroy(Company $company, FlRatio $flRatio)
    {
        if ($flRatio->status == 1) {
            return $this->sendError(__('Active FL ratio can not be deleted.'), 412);
        }

        $flRatio->delete();

        return $this->sendResponse([
            'message' => __('FL ratio deleted successfully.'),
        ]);
    }
}
