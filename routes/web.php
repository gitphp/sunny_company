<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/user', [AdminAuthController::class, 'user']);
            Route::get('/menus', [AdminAuthController::class, 'menus']);

            Route::get('/users/export', [UserController::class, 'export']);
            Route::post('/users/batch-delete', [UserController::class, 'batchDestroy']);
            Route::put('/users/{user}/status', [UserController::class, 'changeStatus']);
            Route::put('/users/{user}/password', [UserController::class, 'resetPassword']);
            Route::apiResource('users', UserController::class);

            Route::get('/system/menus', [MenuController::class, 'index']);
        });
    });
});

Route::view('/admin/{any?}', 'backend')->where('any', '.*');
Route::view('/{any?}', 'frontend')->where('any', '.*');
