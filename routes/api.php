<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ActivityLogController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/scheduled-posts', [PostController::class, 'scheduledPosts']);

    // Posts
    Route::apiResource('posts', PostController::class);


    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::get('/activity-logs/{log}', [ActivityLogController::class, 'show']);
    Route::get('/activity-logs/stats/user', [ActivityLogController::class, 'getUserStats']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    
    // User profile
    Route::get('/user', function () {
        return response()->json([
            'user' => auth()->user(),
            'meta' => [
                'current_time' => '2025-05-22 05:48:07',
                'current_user' => 'Shereen12'
            ]
        ]);
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});