<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Walletable;
use App\Models\WalletTxn;
use App\Services\FreeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCompanyWalletTxns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company;
    protected $startDate;
    protected $endDate;

    public $timeout = 0;

    public function __construct(Company $company, $startDate = null, $endDate = null)
    {
        $this->company = $company;
        $this->startDate = $startDate ?? $company->current_business_year['start_date']->format('Y-m-d');
        $this->endDate = $endDate ?? date('Y-m-d', strtotime('-1 day'));
    }

    public function handle(FreeeService $freeeService)
    {
        $bankAccounts = $this->company->bankAccounts();

        try {
            foreach ($bankAccounts as $bankAccount) {
                $firstTime = true;
                $offset = 0;
                $total = 100;

                while ($firstTime || $offset <= $total) {
                    $response = $freeeService->apiResponse('/wallet_txns?company_id=' . $this->company->id . '&walletable_id=' . $bankAccount->id . '&start_date=' . $this->startDate . '&end_date=' . $this->endDate . '&limit=100&offset=' . $offset, 'wallet_txns');

                    if (count($response['wallet_txns']) == 0) {
                        break;
                    }

                    foreach ($response['wallet_txns'] as $walletTxn) {
                        if ($walletTxn['walletable_type'] != 'bank_account') {
                            continue;
                        }

                        $walletable = Walletable::find(trim($walletTxn['walletable_id']));

                        WalletTxn::updateOrCreate([
                            'id' => trim($walletTxn['id']),
                            'company_id' => $this->company->id,
                        ], [
                            'date' => trim($walletTxn['date']),
                            'walletable_id' => $walletable->id ?? null,
                            'entry_side' => trim($walletTxn['entry_side']),
                            'amount' => is_numeric(trim($walletTxn['amount']))
                                ? trim($walletTxn['amount'])
                                : 0,
                            'balance' => is_numeric(trim($walletTxn['balance']))
                                ? trim($walletTxn['balance'])
                                : 0,
                            'due_amount' => is_numeric(trim($walletTxn['due_amount']))
                                ? trim($walletTxn['due_amount'])
                                : 0,
                            'description' => trim($walletTxn['description']),
                            'status' => trim($walletTxn['status']),
                        ]);
                    }

                    $response = null;
                    $offset = $offset + 100;
                    $total = $total + 100;
                    $firstTime = false;
                    sleep(10);
                }

                $lastBalance = WalletTxn::select('balance')
                    ->where('walletable_id', $bankAccount->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $bankAccount->last_balance = $lastBalance->balance ?? $bankAccount->last_balance;
                $bankAccount->save();

                $this->company->txns_updated_date = $this->endDate;
                $this->company->save();
            }
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'SyncCompanyWalletTxns exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
