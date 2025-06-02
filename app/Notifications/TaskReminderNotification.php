<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Task $task
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');
        $taskUrl = url("/tasks/{$this->task->id}");

        return (new MailMessage)
            ->subject("⏰ Przypomnienie o zadaniu - {$this->task->name}")
            ->greeting("Cześć {$notifiable->name}!")
            ->line("To przypomnienie o zadaniu, które jest zaplanowane na jutro.")
            ->line("**Nazwa zadania:** {$this->task->name}")
            ->line("**Priorytet:** " . $this->getPriorityLabel($this->task->priority))
            ->line("**Termin wykonania:** " . $this->task->due_date->format('d.m.Y'))
            ->when($this->task->description, function ($mail) {
                return $mail->line("**Opis:** {$this->task->description}");
            })
            ->action('Zobacz zadanie', $taskUrl)
            ->line('Powodzenia w realizacji zadania!')
            ->salutation("Pozdrawiam,\nZespół {$appName}");
    }

    private function getPriorityLabel(string $priority): string
    {
        return match($priority) {
            'low' => '🟢 Niski',
            'medium' => '🟡 Średni',
            'high' => '🔴 Wysoki',
            default => $priority
        };
    }
}