<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Walletable;
use App\Services\FreeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCompanyWalletables implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company;

    public $timeout = 0;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function handle(FreeeService $freeeService)
    {
        try {
            $response = $freeeService->apiResponse('/walletables?company_id=' . $this->company->id . '&with_balance=true', 'walletables');

            foreach ($response['walletables'] as $walletable) {
                Walletable::updateOrCreate([
                    'id' => trim($walletable['id']),
                    'company_id' => $this->company->id,
                ], [
                    'name' => trim($walletable['name']),
                    'type' => trim($walletable['type']),
                    'walletable_balance' => trim($walletable['walletable_balance']),
                    'last_balance' => trim($walletable['last_balance']),
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'SyncCompanyWalletables exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
