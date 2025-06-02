<?php

// TYMCZASOWE DEBUG ROUTES - dodaj na samej górze
Route::get('/debug-session', function() {
    return [
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'guard' => auth()->getDefaultDriver(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'session_data' => session()->all(),
    ];
});

Route::get('/debug-login', function() {
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        return [
            'login_result' => 'success',
            'authenticated_after' => auth()->check(),
            'user_id' => auth()->id(),
        ];
    }
    return ['error' => 'no user found'];
});

// Dodaj też test dashboard bez middleware
Route::get('/debug-dashboard', function() {
    $user = \App\Models\User::first();
    try {
        return [
            'user_found' => !!$user,
            'user_id' => $user->id ?? null,
            'stats_method_exists' => method_exists($user, 'getTasksCountByStatus'),
            'overdue_method_exists' => method_exists($user, 'getOverdueTasksCount'),
            'stats' => $user ? $user->getTasksCountByStatus() : null,
            'overdue' => $user ? $user->getOverdueTasksCount() : null,
        ];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
});
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\TaskWebController;
use App\Http\Controllers\Web\SharedTaskWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'processLogin']);
    
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'processRegister']);
});

// Logout route (dostępny dla zalogowanych)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');
});

// Publiczne udostępnianie zadań (bez autoryzacji)
Route::get('/shared-tasks/{token}', [SharedTaskWebController::class, 'show'])->name('shared-task.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [TaskWebController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskWebController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}', [TaskWebController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskWebController::class, 'edit'])->name('tasks.edit');
    
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    
    // AJAX endpoints dla zadań
    Route::prefix('api/web')->group(function () {
        Route::get('/user', [TaskWebController::class, 'apiUser']);
        Route::get('/tasks', [TaskWebController::class, 'apiIndex']);
        Route::post('/tasks', [TaskWebController::class, 'store']);
        Route::get('/tasks/{task}', [TaskWebController::class, 'apiShow']);
        Route::put('/tasks/{task}', [TaskWebController::class, 'update']);
        Route::patch('/tasks/{task}', [TaskWebController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskWebController::class, 'destroy']);
        Route::post('/tasks/{task}/share', [TaskWebController::class, 'generateShareToken']);
        Route::post('/tasks/bulk-update', [TaskWebController::class, 'bulkUpdate']);
    });
});