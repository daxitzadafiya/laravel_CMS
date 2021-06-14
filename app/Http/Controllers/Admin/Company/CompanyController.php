<?php

namespace App\Http\Controllers\Admin\Company;

use App\Events\CompanyConnected;
use App\Events\CompanyDisconnected;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\PaginationResource;
use App\Models\Company;
use App\Services\CompanyService;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    protected $companyService;
    protected $subscriptionService;

    public function __construct(
        CompanyService $companyService,
        SubscriptionService $subscriptionService
    ) {
        $this->companyService = $companyService;
        $this->subscriptionService = $subscriptionService;
    }

    public function index(CompanyRequest $request)
    {
        if ($request->input('search')) {
            $companies = $this->companyService->getCompanies($request);

            return $this->sendResponse(
                ['companies' => CompanyResource::collection($companies)],
                isPaginate($request->input('paginate'))
                    ? ['paginate' => new PaginationResource($companies)]
                    : []
            );
        }

        //return Cache::remember('admin-companies-' . implode('-', $request->validated()), 86400, function () use ($request) {
            $companies = $this->companyService->getCompanies($request);

            return $this->sendResponse(
                ['companies' => CompanyResource::collection($companies)],
                isPaginate($request->input('paginate'))
                    ? ['paginate' => new PaginationResource($companies)]
                    : []
            );
        //});
    }

    public function show(Company $company)
    {
        $company->load('subscriptions', 'prefecture', 'headCount');

        return $this->sendResponse([
            'company' => new CompanyResource($company),
        ]);
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        $data['connected_at'] = empty ($company->connected_at)
            ? $data['status'] == 1 ? NOW() : null
            : $company->connected_at;

        $subscriptionPlanChanged = isset($data['subscription_plan_id']) && $data['subscription_plan_id'] != ($company->active_subscription->id ?? null);
        $companyConnected = isset($data['status']) && $data['status'] == 1 && $company->status != 1;
        $companyDisconnected = isset($data['status']) && $data['status'] == 0 && $company->status == 1;

        $company->update($data);

        if ($subscriptionPlanChanged) {
            $this->subscriptionService->changePlan($company, $data['subscription_plan_id']);
        }

        if ($companyConnected) {
            CompanyConnected::dispatch($company);
        }

        if ($companyDisconnected) {
            CompanyDisconnected::dispatch($company);
        }

        $company->load('subscriptions', 'prefecture', 'headCount');

        return $this->sendResponse([
            'message' => __('Company updated successfully.'),
            'company' => new CompanyResource($company),
        ]);
    }
}
