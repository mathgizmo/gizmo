<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ReportGenerate::class,
        \App\Console\Commands\CheckTestsTimeout::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('report:generate detailed-report')
            ->hourlyAt(59)->unlessBetween('23:00', '7:00');
        $schedule->command('check-test-timeout')
            ->hourlyAt(29)->unlessBetween('23:00', '7:00');
    }
}
