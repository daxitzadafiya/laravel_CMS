<?php

namespace App\Listeners;

use App\Events\CompanyDisconnected;
use App\Models\AccountItem;
use App\Models\Deal;
use App\Models\ManualJournal;
use App\Models\Walletable;
use App\Models\WalletTxn;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveFreeeData implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(CompanyDisconnected $event)
    {
        ManualJournal::where('company_id', $event->company->id)->delete();
        Deal::where('company_id', $event->company->id)->delete();
        WalletTxn::where('company_id', $event->company->id)->delete();
        Walletable::where('company_id', $event->company->id)->delete();
        AccountItem::where('company_id', $event->company->id)->delete();
    }
}
