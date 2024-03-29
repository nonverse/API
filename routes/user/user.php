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
Route::post('/validate-username', [\App\Http\Controllers\User\UserController::class, 'validateUsername'])->middleware('auth.authkey');
Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store'])->middleware('auth.authkey');

/**
 * Authentication required
 */
Route::group(['middleware' => 'auth:api'], function () {
    // User store routes
    Route::prefix('/store')->group(function () {
        // Get user store
        Route::get('/', [\App\Http\Controllers\User\UserController::class, 'get'])->middleware('scope:user.store.read');
        // Update user store
        Route::post('/', [\App\Http\Controllers\User\UserController::class, 'update'])->middleware('scope:user.*');
        // Delete user store
        Route::delete('/', [\App\Http\Controllers\User\UserController::class, 'delete'])->middleware('scope:user.*');
        // Update a user's email address
        Route::post('/email', [\App\Http\Controllers\User\EmailController::class, 'update'])->middleware(['confirmed:update_email', 'scope:user.*']);
        // Update a user's phone number
        Route::post('/phone', [\App\Http\Controllers\User\PhoneController::class, 'update'])->middleware(['confirmed:update_phone', 'scope:user.*']);
    });

    // User security routes
    Route::prefix('/security')->middleware('scope:user.*')->group(function () {
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
            // Update user's recovery email
            Route::post('/email', [\App\Http\Controllers\User\RecoveryController::class, 'updateEmail'])->middleware('confirmed:update_recovery_email');
        });
    });

    // User settings routes
    Route::prefix('/settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\SettingsController::class, 'get'])->middleware('scope:user.settings.read');
        Route::post('/', [\App\Http\Controllers\User\SettingsController::class, 'update'])->middleware('scope:user.settings.update');
    });

    // User services routes
    Route::prefix('/services')->group(base_path('routes/user/services.php'));
});
