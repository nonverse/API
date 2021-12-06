<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User profile API routes that can only be accessed by a session authenticated
| user via an app on the .nonverse.net domain
|--------------------------------------------------------------------------
| Endpoint: /user/profile
|
*/

// Auth required
Route::group(['middleware' => 'auth'], function () {
    // Send an OTP to verify a new user
    Route::post('/verify', [\App\Http\Controllers\Profile\ProfileVerificationController::class, 'sendVerification'])->middleware('throttle:1,1');

    // Create a new user profile
    Route::post('/', [\App\Http\Controllers\Profile\ProfileController::class, 'store']);
    // Delete a user's profile
    Route::delete('/', [\App\Http\Controllers\Profile\ProfileController::class, 'delete']);
});

// Auth or API token required
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/', [\App\Http\Controllers\Profile\ProfileController::class, 'get'])->middleware(['profile', 'ability:profile:view']);
});
