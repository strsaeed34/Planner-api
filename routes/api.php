<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\TimeboxController;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
});

//Admin Routes

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUserRole']);
    Route::get('/users', [AdminController::class, 'listUsers']);
});

//notes Routes

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('notes', NoteController::class);
});

//task routes

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::apiResource('goals', GoalController::class);
});
Route::middleware('auth:sanctum')->post('/tasks/{task}/timebox/{timebox}', [TaskController::class, 'addToTimebox']);



// timebox routes 

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/timebox', [TimeboxController::class, 'index']);
    Route::post('/timebox/today', [TimeboxController::class, 'getOrCreateTodayTimebox']);
    Route::post('/timebox/{timebox}/tasks', [TimeboxController::class, 'assignTask']); // ✅ Assign Task
    Route::get('/timebox/{timebox}', [TimeboxController::class, 'show']); // ✅ Fetch Timebox with Tasks
    Route::delete('/timebox/{timebox}/tasks/{task}', [TimeboxController::class, 'removeTask']); // ✅ Remove Task
});


// user routes

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    route::put('/user', [UserController::class, 'update']);
    Route::get('/user', [UserController::class,'show']);
});
