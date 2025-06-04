<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function updated(Task $task): void
    {
        // Automatyczna synchronizacja przy aktualizacji
        if ($task->sync_to_calendar && $task->isDirty(['name', 'description', 'due_date', 'priority'])) {
            dispatch(function () use ($task) {
                $task->syncToGoogleCalendar();
            })->afterResponse();
        }
    }
    
    public function deleted(Task $task): void
    {
        if ($task->google_event_id) {
            dispatch(function () use ($task) {
                $task->removeFromGoogleCalendar();
            })->afterResponse();
        }
    }
}