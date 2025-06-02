<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Jobs\SendTaskReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Wyślij przypomnienia o zadaniach na jutro';

    public function handle(): int
    {
        $this->info('Rozpoczynam wysyłanie przypomnień...');

        $tasks = Task::dueTomorrow()->get();

        if ($tasks->isEmpty()) {
            $this->info('Brak zadań wymagających przypomnienia.');
            return Command::SUCCESS;
        }

        $this->info("Znaleziono {$tasks->count()} zadań wymagających przypomnienia.");

        $successCount = 0;
        $failCount = 0;

        foreach ($tasks as $task) {
            try {
                SendTaskReminderJob::dispatch($task);
                $successCount++;
                
                $this->line(" Zaplanowano przypomnienie dla zadania: {$task->name} (ID: {$task->id})");
                
            } catch (\Exception $e) {
                $failCount++;
                $this->error(" Błąd dla zadania {$task->id}: " . $e->getMessage());
            }
        }

        $this->info("Zakończono. Sukces: {$successCount}, Błędy: {$failCount}");

        return Command::SUCCESS;
    }
}