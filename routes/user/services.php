<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User service API routes
|--------------------------------------------------------------------------
|
| Endpoint: /user/services
|
*/

/**
 * Get list of all services linked to user's account
 */
Route::get('/', [\App\Http\Controllers\User\ServicesController::class, 'get']);

/**
 * Minecraft Service
 * First party auth required
 */
Route::prefix('/minecraft')->group(function () {
    Route::prefix('/auth')->middleware('scope:*')->group(function () {
        // Validate user's Minecraft username
        Route::post('validate', [\App\Http\Controllers\Services\Minecraft\MinecraftServiceAuthController::class, 'validateUsername']);
    });
});
