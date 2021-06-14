<?php

namespace App\Console\Commands\Freee;

use App\Jobs\SyncCompanyManualJournals;
use App\Models\Company;
use Illuminate\Console\Command;

class SyncManualJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freee:sync-manual-journals {--company=all} {--offset=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync manual journals from freee api';

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

        $companies = $company == 'all'
            ? Company::connected()->get()
            : Company::connected()->where('id', $company)->get();

        foreach ($companies as $company) {
            SyncCompanyManualJournals::dispatchSync($company);
            sleep(10);
        }

        $this->comment('Synced manual journals!');
    }
}
