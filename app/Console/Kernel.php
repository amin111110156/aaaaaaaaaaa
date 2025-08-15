<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('appointments:remind')->dailyAt('08:00');
        $schedule->command('notifications:daily-reports')->dailyAt('09:00');
        $schedule->command('notifications:patient-reminders')->dailyAt('10:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}