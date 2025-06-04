<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\TaskWebController;
use App\Http\Controllers\Web\SharedTaskController;
use App\Http\Controllers\Web\TaskVersionWebController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'processLogin']);
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'processRegister']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    
    Route::get('/tasks', [TaskWebController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskWebController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}', [TaskWebController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskWebController::class, 'edit'])->name('tasks.edit');
    
    Route::get('/tasks/{task}/versions/{version}', [TaskVersionWebController::class, 'show'])->name('tasks.versions.show');
    
    Route::prefix('api/web')->group(function () {
        Route::get('/tasks', [TaskWebController::class, 'apiIndex'])->name('api.tasks.index');
        Route::post('/tasks', [TaskWebController::class, 'store'])->name('api.tasks.store');
        Route::get('/tasks/{task}', [TaskWebController::class, 'apiShow'])->name('api.tasks.show');
        Route::put('/tasks/{task}', [TaskWebController::class, 'update'])->name('api.tasks.update.put');
        Route::patch('/tasks/{task}', [TaskWebController::class, 'update'])->name('api.tasks.update');
        Route::delete('/tasks/{task}', [TaskWebController::class, 'destroy'])->name('api.tasks.destroy');
        Route::post('/tasks/{task}/share', [TaskWebController::class, 'generateShareToken'])->name('api.tasks.share');
        Route::post('/tasks/bulk-update', [TaskWebController::class, 'bulkUpdate'])->name('api.tasks.bulk-update');
        Route::get('/tasks/{task}/history', [TaskWebController::class, 'history'])->name('api.tasks.history');
        Route::get('/user', [TaskWebController::class, 'apiUser'])->name('api.user');
        Route::post('/tasks/{task}/sync-calendar', [TaskWebController::class, 'syncToCalendar'])->name('api.tasks.sync-calendar');
        Route::delete('/tasks/{task}/sync-calendar', [TaskWebController::class, 'unsyncFromCalendar'])->name('api.tasks.unsync-calendar');

    });
});

Route::get('/shared-tasks/{token}', [SharedTaskController::class, 'show'])->name('shared.task.show');