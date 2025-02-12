<?php

namespace App\Providers;

use App\Jobs\CreateBackupJob;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule): void
    {
        $schedule->command('app:archive-logs')->everyMinute();

        // Monthly backups
        // Monthly backups on specific days
        $schedule->job(new CreateBackupJob('db'))
            ->monthly()
            ->when(function () {
                return in_array(date('d'), ['01', '11', '21']);
            })
            ->at('01:00');

        $schedule->job(new CreateBackupJob('files'))
            ->monthly()
            ->when(function () {
                return in_array(date('d'), ['05', '15', '25']);
            })
            ->at('02:00');

        $schedule->job(new CreateBackupJob('full'))
            ->monthly()
            ->when(function () {
                return in_array(date('d'), ['10', '20', '30']);
            })
            ->at('03:00');
    }
}
