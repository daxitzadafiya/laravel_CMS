<?php

namespace App\Console\Commands\Freee;

use App\Jobs\SyncCompanyAccountItems;
use App\Jobs\SyncCompanyDeals;
use App\Jobs\SyncCompanyManualJournals;
use App\Jobs\SyncCompanyWalletables;
use App\Jobs\SyncCompanyWalletTxns;
use App\Models\Company;
use Illuminate\Console\Command;

class DailyCompanyDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freee:daily-sync {--company=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync last 1 month company data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $company = $this->option('company');
        $startDate = date('Y-m-01', strtotime('-1 month'));
        $endDate = date('Y-m-d', strtotime('-1 day'));

        $companies = $company == 'all'
            ? Company::connected()->where('freee_syncing', 0)->whereDate('deals_updated_date', '!=', $endDate)->limit(3)->get()
            : Company::connected()->where('id', $company)->get();

        foreach ($companies as $company) {
            if ($company->id == 1) {
                continue;
            }

            SyncCompanyAccountItems::dispatch($company);
            SyncCompanyWalletables::dispatch($company);
            SyncCompanyWalletTxns::dispatch($company, $startDate, $endDate);
            SyncCompanyManualJournals::dispatch($company, $startDate, $endDate);
            SyncCompanyDeals::dispatch($company, $startDate, $endDate);

            $company->freee_syncing = 1;
            $company->save();
        }
    }
}
