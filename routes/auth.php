<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth API routes
|--------------------------------------------------------------------------
|
| Endpoint: /auth
|
*/

/**
 * Authentication required
 */
Route::group(['middleware' => 'auth:api'], function () {
    // Send verification code
    Route::post('send-verification', [\App\Http\Controllers\VerificationController::class, 'send']);
});
