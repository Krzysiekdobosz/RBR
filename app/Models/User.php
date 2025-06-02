<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Summary of hidden
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Summary of tasks
     * @return HasMany<Task, User>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Summary of getTasksCountByStatus
     * @return array{done: int, in_progress: int, to-do: int}
     */
    public function getTasksCountByStatus(): array
    {
        return [
            'to-do' => $this->tasks()->where('status', 'to-do')->count(),
            'in_progress' => $this->tasks()->where('status', 'in_progress')->count(),
            'done' => $this->tasks()->where('status', 'done')->count(),
        ];
    }

    /**
     * Summary of getOverdueTasksCount
     * @return int
     */
    public function getOverdueTasksCount(): int
    {
        return $this->tasks()->overdue()->count();
    }
}