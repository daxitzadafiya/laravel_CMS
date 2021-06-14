<?php

namespace App\Providers;

use App\Events\CompanyConnected;
use App\Events\CompanyDisconnected;
use App\Listeners\RemoveFreeeData;
use App\Listeners\SyncFreeeData;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CompanyConnected::class => [
            SyncFreeeData::class,
        ],
        CompanyDisconnected::class => [
            RemoveFreeeData::class,
            // Remove all personal tokens if disconnected
        ],
        'App\Events\SendBrowserNotify' => [
            'App\Listeners\SendBrowserNotifyMessage',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
