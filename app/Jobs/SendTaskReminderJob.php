<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTaskReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        protected Task $task
    ) {}

    public function handle(): void
    {
        try {
            if (!$this->task->exists || $this->task->reminder_sent) {
                Log::info("Zadanie {$this->task->id} nie istnieje lub przypomnienie już wysłane");
                return;
            }

            if ($this->task->status === 'done') {
                Log::info("Zadanie {$this->task->id} zostało zakończone - pomijam przypomnienie");
                return;
            }

            $this->task->user->notify(new TaskReminderNotification($this->task));

            $this->task->update(['reminder_sent' => true]);

            Log::info("Wysłano przypomnienie dla zadania {$this->task->id}");
            
        } catch (\Exception $e) {
            Log::error("Błąd podczas wysyłania przypomnienia dla zadania {$this->task->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Nie udało się wysłać przypomnienia dla zadania {$this->task->id}: " . $exception->getMessage());
    }
}