<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('student/dashboard', [DashboardController::class, 'student'])->middleware('role:student');
        Route::get('teacher/dashboard', [DashboardController::class, 'teacher'])->middleware('role:teacher');
        Route::get('parent/dashboard', [DashboardController::class, 'parent'])->middleware('role:parent');
        Route::get('admin/dashboard', [DashboardController::class, 'admin'])->middleware('role:admin');
    });
});