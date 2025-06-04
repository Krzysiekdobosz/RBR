<?php

namespace App\Services;

use App\Models\Task;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    public function createEvent(Task $task): bool
    {
        $event = new Event;
        
        $event->name = $task->name;
        $event->description = $this->buildEventDescription($task);
        
        if ($task->due_date) {
            $startDateTime = Carbon::parse($task->due_date)->setTime(9, 0);
            $endDateTime = Carbon::parse($task->due_date)->setTime(10, 0);
            
            $event->startDateTime = $startDateTime;
            $event->endDateTime = $endDateTime;
        } else {
            $event->startDateTime = Carbon::now()->setTime(9, 0);
            $event->endDateTime = Carbon::now()->setTime(10, 0);
        }
        
        $event->addAttendee(['email' => $task->user->email]);
        
        $event->colorId = $this->getColorIdByPriority($task->priority);
        
        $googleEvent = $event->save();
        
        $task->update([
            'google_event_id' => $googleEvent->id,
            'calendar_synced_at' => now()
        ]);
        
        return true;
    }
    
    public function updateEvent(Task $task): bool
    {
        $event = Event::find($task->google_event_id);
        
        if (!$event) {
            return $this->createEvent($task);
        }
        
        $event->name = $task->name;
        $event->description = $this->buildEventDescription($task);
        
        if ($task->due_date) {
            $startDateTime = Carbon::parse($task->due_date)->setTime(9, 0);
            $endDateTime = Carbon::parse($task->due_date)->setTime(10, 0);
            
            $event->startDateTime = $startDateTime;
            $event->endDateTime = $endDateTime;
        }
        
        $event->colorId = $this->getColorIdByPriority($task->priority);
        
        $event->save();
        
        $task->update(['calendar_synced_at' => now()]);
        
        return true;
    }
    
    public function deleteEvent(Task $task): bool
    {
        $event = Event::find($task->google_event_id);
        
        if ($event) {
            $event->delete();
        }
        
        $task->update([
            'google_event_id' => null,
            'sync_to_calendar' => false,
            'calendar_synced_at' => null
        ]);
        
        return true;
    }
    
    private function buildEventDescription(Task $task): string
    {
        $description = '';
        
        if ($task->description) {
            $description .= $task->description . "\n\n";
        }
        
        $description .= "Status: " . ucfirst($task->status) . "\n";
        $description .= "Priorytet: " . ucfirst($task->priority) . "\n";
        
        if ($task->share_token) {
            $description .= "\nLink do zadania: " . url('/shared-tasks/' . $task->share_token);
        }
        
        return $description;
    }
    
    private function getColorIdByPriority(string $priority): int
    {
        return match($priority) {
            'high' => 11,
            'medium' => 5,
            'low' => 10,     
            default => 1,    
        };
    }
}