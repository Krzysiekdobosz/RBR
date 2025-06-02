<?php
// app/Models/SharedTaskToken.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SharedTaskToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'token',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Scope dla aktywnych tokenów
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', Carbon::now());
    }

    // Sprawdź czy token jest ważny
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at > Carbon::now();
    }

    // Dezaktywuj token
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}