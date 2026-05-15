<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan sinkronisasi media setiap jam 02:00 pagi
        $schedule->command('media:sync-daily')->dailyAt('02:00')->withoutOverlapping();

        // Jalankan backup database ke Telegram setiap jam 03:00 pagi
        $schedule->command('db:backup-telegram')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
