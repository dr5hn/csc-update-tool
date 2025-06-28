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
        // Sync geographical data weekly (every Sunday at 2 AM)
        $schedule->command('geo:sync --force')
                 ->weekly()
                 ->sundays()
                 ->at('02:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->emailOutputOnFailure(config('mail.admin_email', 'admin@example.com'));

        // Run the queue worker every minute to process queued jobs
        // This will process our delayed notifications
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

        // We can also add a command to retry failed jobs periodically
        $schedule->command('queue:retry all')->hourly();
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
