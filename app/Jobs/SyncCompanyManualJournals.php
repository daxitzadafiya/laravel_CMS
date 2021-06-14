<?php

namespace App\Jobs;

use App\Models\AccountItem;
use App\Models\Company;
use App\Models\ManualJournal;
use App\Services\FreeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCompanyManualJournals implements ShouldQueue
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
            ManualJournal::where('company_id', $this->company->id)
                ->whereBetween('issue_date', [$this->startDate, $this->endDate])
                ->delete();

            $firstTime = true;
            $offset = 0;
            $total = 500;

            while ($firstTime || $offset <= $total) {
                $response = $freeeService->apiResponse('/manual_journals?company_id=' . $this->company->id . '&start_issue_date=' . $this->startDate . '&end_issue_date=' . $this->endDate . '&limit=500&offset=' . $offset, 'manual_journals');

                if (count($response['manual_journals']) == 0) {
                    break;
                }

                foreach ($response['manual_journals'] as $journal) {
                    foreach ($journal['details'] as $detail) {
                        $accountItem = AccountItem::find(trim($detail['account_item_id']));

                        ManualJournal::create([
                            'id' => trim($detail['id']),
                            'company_id' => trim($journal['company_id']),
                            'issue_date' => trim($journal['issue_date']),
                            'account_item_id' => $accountItem->id ?? null,
                            'amount' => trim($detail['amount']),
                            'vat' => trim($detail['vat']),
                            'description' => trim($detail['description']),
                            'entry_side' => trim($detail['entry_side']),
                        ]);
                    }
                }

                $response = null;
                $offset = $offset + 500;
                $total = $total + 500;
                $firstTime = false;

                sleep(10);
            }
        } catch (\Exception $e) {
            Log::channel('freee-api')->info([
                'message' => 'SyncCompanyManualJournals exception',
                'error' => $e->getMessage(),
            ]);

            dd($e->getMessage());
        }
    }
}
