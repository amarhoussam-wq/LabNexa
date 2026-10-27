<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Send appointment reminders (standard)
        $schedule->command('reminders:send')->hourly();

        // Send preparation-specific reminders exactly 12 hours before
        $schedule->command('app:send-prep-reminders')->hourly();

        // Alternative: Send reminders twice daily (morning and evening)
        // $schedule->command('reminders:send')->twiceDaily(9, 17);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
