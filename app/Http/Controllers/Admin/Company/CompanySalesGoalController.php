<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalesGoalRequest;
use App\Http\Resources\MonthGoalResource;
use App\Http\Resources\SalesGoalResource;
use App\Models\Company;
use App\Models\SalesGoal;
use App\Services\CompanyService;
use App\Services\SalesService;
use Illuminate\Support\Facades\DB;

class CompanySalesGoalController extends Controller
{
    protected $companyService;
    protected $salesService;

    public function __construct(
        CompanyService $companyService,
        SalesService $salesService
    ) {
        $this->salesService = $salesService;
        $this->companyService = $companyService;
    }

    public function index(Company $company, SalesGoalRequest $request)
    {
        $businessYear = $request->getBusinessYear();

        $saleGoals = SalesGoal::with('values')
            ->where('company_id', $company->id)
            ->where('business_year_start', $businessYear['start_date'])
            ->where('business_year_end', $businessYear['end_date'])
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                return $query->orderBy($sort, $request->input('order'));
            })
            ->get();

        return $this->sendResponse([
            'business_year' => [
                'term' => $businessYear['term'],
                'start_date' => $businessYear['start_date']->format('Y-m-d'),
                'end_date' => $businessYear['end_date']->format('Y-m-d'),
            ],
            'total_sale' => $this->salesService->getTotalSale($company, $businessYear),
            'goals' => SalesGoalResource::collection($saleGoals),
        ]);
    }

    public function show(Company $company, SalesGoalRequest $request)
    {
        $businessYear = $request->getBusinessYear();

        $saleGoals = $this->companyService->getMonthlySalesGoals($company, $businessYear);

        $monthlyIncomeDeals = $this->companyService->getMonthlyDealsByAccountItemType($company, $businessYear, 'income');

        foreach ($saleGoals as $saleGoal) {
            $saleGoal->sale = $monthlyIncomeDeals->where('year_month', $saleGoal->year_month)->first()->income ?? 0;
        }

        $response = [
            'business_year' => [
                'term' => $businessYear['term'],
                'start_date' => $businessYear['start_date']->format('Y-m-d'),
                'end_date' => $businessYear['end_date']->format('Y-m-d'),
                'total_sale' => $monthlyIncomeDeals->sum('income'),
            ],
            'goals' => MonthGoalResource::collection($saleGoals),
        ];

        return $this->sendResponse($response);
    }

    public function store(Company $company, SalesGoalRequest $request)
    {
        $data = $request->validated();
        $businessYear = $request->getBusinessYear();

        DB::transaction(function () use ($company, $data, $businessYear) {
            $activeSaleGoal = $this->companyService->getActiveSalesGoal($company, $businessYear);

            $saleGoal = $company->salesGoals()->create([
                'business_year_start' => $businessYear['start_date'],
                'business_year_end' => $businessYear['end_date'],
                'user_id' => auth()->user()->id,
                'status' => $activeSaleGoal ? 0 : 1,
            ]);

            $goals = [];

            foreach ($data['goals'] as $goal) {
                $monthSplit = explode('-', $goal['month']);

                $goals[] = [
                    'year' => $monthSplit[0],
                    'month' => $monthSplit[1],
                    'goal' => $goal['goal'],
                ];
            }

            $saleGoal->values()->createMany($goals);
        });

        return $this->sendResponse([
            'message' => __('Sales goals added successfully.'),
        ]);
    }

    public function destroy(Company $company, SalesGoal $salesGoal)
    {
        if ($salesGoal->status == 1) {
            return $this->sendError(__('Active goal can not be deleted.'), 412);
        }

        $salesGoal->delete();

        return $this->sendResponse([
            'message' => __('Goal deleted successfully.'),
        ]);
    }
}
