<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\SharedTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Publiczne endpointy
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Udostępnione zadania (bez autoryzacji)
Route::prefix('shared-tasks')->group(function () {
    Route::get('{token}', [SharedTaskController::class, 'show']);
});

// Endpointy wymagające autoryzacji
Route::middleware('auth:sanctum')->group(function () {
    
    // Autoryzacja
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });

    // Zadania
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('{task}', [TaskController::class, 'show']);
        Route::put('{task}', [TaskController::class, 'update']);
        Route::patch('{task}', [TaskController::class, 'update']);
        Route::delete('{task}', [TaskController::class, 'destroy']);
        
        // Generowanie tokenu udostępniania
        Route::post('{task}/share', [TaskController::class, 'generateShareToken']);
        
        // Operacje grupowe
        Route::post('bulk-update', [TaskController::class, 'bulkUpdate']);
    });

    // Zarządzanie tokenami udostępniania
    Route::prefix('shared-tokens')->group(function () {
        Route::delete('{token}', [SharedTaskController::class, 'deactivate']);
    });
});

// Fallback dla nieistniejących endpointów
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint nie istnieje'
    ], 404);
});