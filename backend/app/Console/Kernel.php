<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\RunMrpJob;
use App\Jobs\RecomputeForecastsJob;
use App\Jobs\EmailReportJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daily MRP run at 2:00 AM
        $schedule->job(new RunMrpJob())
                ->dailyAt('02:00')
                ->withoutOverlapping()
                ->runInBackground();

        // Daily forecast recomputation at 3:00 AM
        $schedule->job(new RecomputeForecastsJob())
                ->dailyAt('03:00')
                ->withoutOverlapping()
                ->runInBackground();

        // Weekly report emails on Sundays at 6:00 AM
        $schedule->job(new EmailReportJob('inventory'))
                ->weekly()
                ->sundays()
                ->at('06:00')
                ->runInBackground();

        $schedule->job(new EmailReportJob('production'))
                ->weekly()
                ->sundays()
                ->at('06:15')
                ->runInBackground();

        $schedule->job(new EmailReportJob('sales'))
                ->weekly()
                ->sundays()
                ->at('06:30')
                ->runInBackground();

        $schedule->job(new EmailReportJob('forecast'))
                ->weekly()
                ->sundays()
                ->at('06:45')
                ->runInBackground();
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