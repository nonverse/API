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
        // Update a user's email address
        Route::post('/email', [\App\Http\Controllers\User\EmailController::class, 'update'])->middleware('confirmed:update_email');
        // Update a user's phone number
        Route::post('/phone', [\App\Http\Controllers\User\PhoneController::class, 'update'])->middleware('confirmed:update_phone');
    });

    // User security routes
    Route::prefix('/security')->group(function () {
        // User Two-Step routes
        Route::prefix('/two-step')->group(function () {
            // Get Two-Step setup data
            Route::get('/', [\App\Http\Controllers\User\TwoFactorController::class, 'get']);
            Route::post('/', [\App\Http\Controllers\User\TwoFactorController::class, 'enable'])->middleware('confirmed:update_two_step_login');
        });

        // Update password
        Route::post('/password', [\App\Http\Controllers\User\PasswordController::class, 'update'])->middleware('confirmed:update_password');

        // User recovery routes
        Route::prefix('/recovery')->group(function () {
            // Get user recovery details
            Route::get('/', [\App\Http\Controllers\User\RecoveryController::class, 'get']);
        });
    });

    Route::prefix('/settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\SettingsController::class, 'get']);
        Route::post('/', [\App\Http\Controllers\User\SettingsController::class, 'update']);
    });
});
