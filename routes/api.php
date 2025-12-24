<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Http\Request;

Route::get('/hello', function () {
    return "Hello World";
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/user', [UserController::class, 'getUser']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::apiResource('tasks', TaskController::class)->only(['index', 'store']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
});
