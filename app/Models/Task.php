<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'priority',
        'status',
        'due_date',
        'reminder_sent'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sharedTokens(): HasMany
    {
        return $this->hasMany(SharedTaskToken::class);
    }

    // Scope dla filtrowania
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByPriority($query, $priority)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    public function scopeByDueDate($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('due_date', '>=', $from);
        }
        if ($to) {
            $query->where('due_date', '<=', $to);
        }
        return $query;
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
                    ->where('status', '!=', 'done');
    }

    public function scopeDueTomorrow($query)
    {
        return $query->where('due_date', Carbon::tomorrow())
                    ->where('status', '!=', 'done')
                    ->where('reminder_sent', false);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < Carbon::today() && $this->status !== 'done';
    }

    public function getDaysUntilDueAttribute(): int
    {
        return Carbon::today()->diffInDays($this->due_date, false);
    }

    // Helper methods
    public function generateShareToken(int $expiryHours = 24): SharedTaskToken
    {
        // Dezaktywuj poprzednie tokeny
        $this->sharedTokens()->update(['is_active' => false]);

        return $this->sharedTokens()->create([
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => Carbon::now()->addHours($expiryHours),
            'is_active' => true,
        ]);
    }
}