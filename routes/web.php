<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\TaskWebController;
use App\Http\Controllers\Web\SharedTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trasy dla gości
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'processLogin']);
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'processRegister']);
});

// Trasy dla zalogowanych użytkowników
Route::middleware('auth')->group(function () {
    // Dashboard i profil
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    
    // Wylogowanie
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
    
    // Zadania - widoki
    Route::get('/tasks', [TaskWebController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskWebController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}', [TaskWebController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskWebController::class, 'edit'])->name('tasks.edit');
    
    // API endpoints dla zadań (używane przez AJAX)
    Route::prefix('api/web')->group(function () {
        Route::get('/tasks', [TaskWebController::class, 'apiIndex'])->name('api.tasks.index');
        Route::post('/tasks', [TaskWebController::class, 'store'])->name('api.tasks.store');
        Route::get('/tasks/{task}', [TaskWebController::class, 'apiShow'])->name('api.tasks.show');
        Route::patch('/tasks/{task}', [TaskWebController::class, 'update'])->name('api.tasks.update');
        Route::delete('/tasks/{task}', [TaskWebController::class, 'destroy'])->name('api.tasks.destroy');
        Route::post('/tasks/{task}/share', [TaskWebController::class, 'generateShareToken'])->name('api.tasks.share');
        Route::post('/tasks/bulk-update', [TaskWebController::class, 'bulkUpdate'])->name('api.tasks.bulk-update');
        Route::get('/user', [TaskWebController::class, 'apiUser'])->name('api.user');
    });
});

// Udostępnione zadania (bez autoryzacji)
Route::get('/shared-tasks/{token}', [SharedTaskController::class, 'show'])->name('shared.task.show');