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
    Route::post('/verify', [\App\Http\Controllers\Profile\ProfileVerificationController::class, 'sendVerification']);


});

// Auth or API token required
Route::group(['middleware' => 'auth:sanctum'], function () {
    //
});
