<?php

namespace App\Listeners;

use App\Events\CompanyConnected;
use App\Jobs\SyncCompanyAccountItems;
use App\Jobs\SyncCompanyDeals;
use App\Jobs\SyncCompanyManualJournals;
use App\Jobs\SyncCompanyWalletables;
use App\Jobs\SyncCompanyWalletTxns;

class SyncFreeeData
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyConnected $event)
    {
        SyncCompanyAccountItems::dispatch($event->company);
        SyncCompanyWalletables::dispatch($event->company);
        SyncCompanyWalletTxns::dispatch($event->company);
        SyncCompanyDeals::dispatch($event->company);
        SyncCompanyManualJournals::dispatch($event->company);
    }
}
