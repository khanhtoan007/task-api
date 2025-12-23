<?php

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