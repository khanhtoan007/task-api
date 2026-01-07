<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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
    Route::post('projects/{project}/assign-user', [ProjectController::class, 'assignUser']);
    Route::apiResource('tasks', TaskController::class);
    Route::get('projects/{project}/tasks', [TaskController::class, 'getTasksByProject']);

    //User Contest
    Route::get('users/contests', [UserController::class, 'getAllContestUser']);
    Route::delete('users/{user}', [UserController::class, 'delete']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::post('users/contests', [UserController::class, 'createUserContest']);

    // Contest
    Route::apiResource('contests', ContestController::class);
});
