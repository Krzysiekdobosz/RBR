<?php

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

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Summary of task
     * @return BelongsTo<Task, SharedTaskToken>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Summary of scopeActive
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Summary of isValid
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at > Carbon::now();
    }

    /**
     * Summary of deactivate
     * @return void
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}