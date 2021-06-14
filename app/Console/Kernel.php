<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Commands/Freee');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('freee:refresh-access-token')
            ->twiceDaily(13, 23);

        $schedule->command('freee:sync-companies')
            ->dailyAt('01:00');

        $schedule->command('freee:daily-sync')
            ->everyFifteenMinutes()
            ->between('1:15', '06:00');

        $schedule->command('demo-company-setup')
            ->dailyAt('06:30');
    }
}
