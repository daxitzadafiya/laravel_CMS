<?php

namespace App\Http\Controllers\User\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\WalletTransactionResource;
use App\Models\Company;
use App\Models\Walletable;
use App\Services\ReportService;
use Illuminate\Http\Request;

class WalletTransactionReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Walletable $walletable, Request $request)
    {
        abort_if($walletable->company_id != auth()->user()->company_id, 404);

        $request->validate([
            'date_from' => ['required', 'string', 'date_format:Y-m-d'],
            'date_to' => ['required', 'string', 'date_format:Y-m-d'],
        ]);

        $company = Company::select('id', 'display_name', 'business_year_start_month', 'business_year_start_day', 'registration_date', 'txns_updated_date')->findorFail(auth()->user()->company_id);

        $transactions = $this->reportService->bankTransactionReport($company, $walletable, $request);

        return $this->sendResponse(
            [
                'date' => $company->txns_updated_date->format('Y-m-d'),
                'transactions' => WalletTransactionResource::collection($transactions)
            ],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($transactions)]
                : []
        );
    }
}
