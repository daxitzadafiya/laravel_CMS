<?php

namespace App\Jobs;

use App\Models\AccountItem;
use App\Models\Company;
use App\Models\Deal;
use App\Services\FreeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCompanyDeals implements ShouldQueue
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
        try {
            Deal::where('company_id', $this->company->id)
                ->whereBetween('issue_date', [$this->startDate, $this->endDate])
                ->delete();

            $firstTime = true;
            $offset = 0;
            $total = 100;

            while ($firstTime || $offset <= $total) {
                $response = $freeeService->apiResponse('/deals?company_id=' . $this->company->id . '&start_issue_date=' . $this->startDate . '&end_issue_date=' . $this->endDate . '&limit=100&offset=' . $offset, 'deals');

                $total = $response['meta']['total_count'] ?? $total;

                foreach ($response['deals'] as $deal) {
                    foreach ($deal['details'] as $detail) {
                        $accountItem = AccountItem::find(trim($detail['account_item_id']));

                        Deal::create([
                            'id' => trim($detail['id']),
                            'company_id' => $this->company->id,
                            'issue_date' => !empty(trim($deal['issue_date']))
                                ? trim($deal['issue_date'])
                                : null,
                            'type' => trim($deal['type']),
                            'account_item_id' => $accountItem->id ?? null,
                            'amount' => trim($detail['amount']),
                            'vat' => trim($detail['vat']),
                            'description' => trim($detail['description']),
                            'entry_side' => trim($detail['entry_side']),
                        ]);
                    }
                }

                $response = null;
                $offset = $offset + 100;
                $firstTime = false;
                $this->company->deals_updated_date = $this->endDate;
                $this->company->save();

                sleep(10);
            }

            $this->company->freee_syncing = 0;
            $this->company->save();
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'SyncCompanyDeals exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
