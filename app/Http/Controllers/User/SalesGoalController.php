<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\GoalRequest;
use App\Http\Resources\MonthGoalResource;
use App\Models\Company;
use App\Models\SalesGoalValue;
use App\Services\CompanyService;
use Illuminate\Support\Facades\DB;

class SalesGoalController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $company = Company::select('id', 'display_name', 'business_year_start_month', 'business_year_start_day', 'registration_date')->findorFail(auth()->user()->company_id);

        $monthlyIncomeDeals = $this->companyService->getMonthlyDealsByAccountItemType($company, $company->current_business_year, 'income');

        $goals = $this->companyService->getMonthlySalesGoals($company, $company->current_business_year);

        foreach ($goals as $goal) {
            $goal->sale = $monthlyIncomeDeals->where('year_month', $goal->year_month)->first()->income ?? 0;
        }

        $response = [
            'business_year' => [
                'term' => $company->current_business_year['term'],
                'start_date' => $company->current_business_year['start_date']->format('Y-m-d'),
                'end_date' => $company->current_business_year['end_date']->format('Y-m-d'),
            ],
            'previous_term' => null,
            'goals' => MonthGoalResource::collection($goals),
        ];

        if ($company->current_business_year['term'] - 1 > 0) {
            $response['previous_term'] = [
                'term' => $company->current_business_year['term'] - 1,
                'sale' => 0,
            ];
        }

        return $this->sendResponse($response);
    }

    public function update(GoalRequest $request)
    {
        $company = Company::select('id', 'display_name', 'business_year_start_month', 'business_year_start_day', 'registration_date')->findorFail(auth()->user()->company_id);

        $data = $request->validated();

        DB::transaction(function () use ($company, $data) {
            $activeSaleGoal = $this->companyService->getActiveSalesGoal($company, $company->current_business_year);

            if (!$activeSaleGoal) {
                $activeSaleGoal = $company->salesGoals()->create([
                    'business_year_start' => $company->current_business_year['start_date'],
                    'business_year_end' => $company->current_business_year['end_date'],
                    'user_id' => auth()->user()->id,
                    'status' => 1,
                ]);
            }

            $goals = [];

            foreach ($data['goals'] as $goal) {
                $monthSplit = explode('-', $goal['month']);

                $goals[] = [
                    'sales_goal_id' => $activeSaleGoal->id,
                    'year' => $monthSplit[0],
                    'month' => $monthSplit[1],
                    'goal' => $goal['goal'],
                ];
            }

            SalesGoalValue::upsert($goals, ['sale_goal_id', 'year', 'month']);
        });

        return $this->sendResponse([
            'message' => __('Goals updated successfully.'),
        ]);
    }
}
