<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /user
|
*/

// Create new user
Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store'])->middleware('auth.authkey');

/**
 * Authentication required
 */
Route::group(['middleware' => 'auth:api'], function () {
    // User store routes
    Route::prefix('/store')->group(function () {
        // Get user store
        Route::get('/', [\App\Http\Controllers\User\UserController::class, 'get']);
        // Update user store
        Route::post('/', [\App\Http\Controllers\User\UserController::class, 'update']);
        // Delete user store
        Route::delete('/', [\App\Http\Controllers\User\UserController::class, 'delete']);
    });

    // User security routes
    Route::prefix('/security')->group(function () {
        // User recovery routes
        Route::prefix('/recovery')->group(function () {
            // Get user recovery details
            Route::get('/', [\App\Http\Controllers\User\RecoveryController::class, 'get']);
        });

        // User Two-Step routes
        Route::prefix('/two-step')->group(function () {
            // Get Two-Step setup data
            Route::get('/', [\App\Http\Controllers\User\TwoFactorController::class, 'get']);
            Route::post('/', [\App\Http\Controllers\User\TwoFactorController::class, 'enable'])->middleware('confirmed:update_two_step_login');
        });
    });


});
