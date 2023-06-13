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
});
