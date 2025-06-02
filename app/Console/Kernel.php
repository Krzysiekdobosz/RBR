<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('tasks:send-reminders')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure(config('mail.from.address'));

        // Opcjonalnie: czyszczenie wygasłych tokenów co tydzień
        $schedule->call(function () {
            \App\Models\SharedTaskToken::where('expires_at', '<', now())
                ->orWhere('is_active', false)
                ->delete();
        })->weekly()->sundays()->at('02:00');

    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}