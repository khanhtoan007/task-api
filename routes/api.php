<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello World';
});

Route::middleware(['auth:sanctum'])->group(function (): void {
    // Route::get('/user', [UserController::class, 'getUser']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::prefix('auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::middleware(['auth:api'])->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function (): void {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);

    Route::prefix('projects/{project}')->group(function (): void {
        Route::get('members', [ProjectMemberController::class, 'index']);
        Route::post('members', [ProjectMemberController::class, 'store']);
        Route::patch('members/{member}', [ProjectMemberController::class, 'update']);
        Route::delete('members/{member}', [ProjectMemberController::class, 'destroy']);

        // Route::get('tasks', [TaskController::class, 'index']);
        // Route::post('tasks', [TaskController::class, 'store']);
        // Route::patch('tasks/{task}', [TaskController::class, 'update']);
        // Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
    });
});
