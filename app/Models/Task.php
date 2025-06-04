<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 
        'priority', 'status', 'due_date', 
        'reminder_sent', 'share_token',
        'google_event_id', 'sync_to_calendar',
        'calendar_synced_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_sent' => 'boolean',
        'sync_to_calendar' => 'boolean',
        'calendar_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TaskVersion::class)->orderBy('created_at', 'desc');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'done');
    }

    public function scopeDueTomorrow(Builder $query): Builder
    {
        return $query->whereDate('due_date', Carbon::tomorrow());
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'done';
    }

    public function getDaysUntilDueAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function syncToGoogleCalendar(): bool
    {
        try {
            if ($this->google_event_id) {
                // Aktualizuj istniejące wydarzenie
                return app('App\Services\GoogleCalendarService')->updateEvent($this);
            } else {
                // Stwórz nowe wydarzenie
                return app('App\Services\GoogleCalendarService')->createEvent($this);
            }
        } catch (\Exception $e) {
            Log::error('Google Calendar sync failed: ' . $e->getMessage());
            return false;
        }
    }

    public function removeFromGoogleCalendar(): bool
    {
        if (!$this->google_event_id) {
            return true;
        }

        try {
            return app('App\Services\GoogleCalendarService')->deleteEvent($this);
        } catch (\Exception $e) {
            Log::error('Google Calendar removal failed: ' . $e->getMessage());
            return false;
        }
    }

    public function createVersion(array $oldAttributes = null): TaskVersion
    {
        $changes = [];
        
        if ($oldAttributes) {
            $trackableFields = ['name', 'description', 'priority', 'status', 'due_date', 'reminder_sent'];
            
            foreach ($trackableFields as $field) {
                $oldValue = $oldAttributes[$field] ?? null;
                $newValue = $this->getAttribute($field);
                
                if ($this->isDifferent($oldValue, $newValue)) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        return $this->versions()->create([
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'reminder_sent' => $this->reminder_sent,
            'created_at' => now(),
            'changes' => empty($changes) ? null : $changes,
        ]);
    }

    private function isDifferent($oldValue, $newValue): bool
    {
        if ($oldValue instanceof \Carbon\Carbon || $newValue instanceof \Carbon\Carbon) {
            $old = $oldValue ? $oldValue->format('Y-m-d') : null;
            $new = $newValue ? $newValue->format('Y-m-d') : null;
            return $old !== $new;
        }

        if (is_bool($oldValue) || is_bool($newValue)) {
            return (bool) $oldValue !== (bool) $newValue;
        }

        $old = $oldValue === '' ? null : $oldValue;
        $new = $newValue === '' ? null : $newValue;

        return $old !== $new;
    }

    public function save(array $options = [])
    {
        $createVersion = $options['create_version'] ?? true;
        
        if ($createVersion && $this->exists) {
            $oldAttributes = $this->getOriginal();
        }

        $result = parent::save($options);

        if ($createVersion && $this->exists && isset($oldAttributes) && $this->wasChanged()) {
            $this->createVersion($oldAttributes);
        }

        return $result;
    }
}