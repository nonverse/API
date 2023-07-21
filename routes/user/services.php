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
 * Minecraft Service
 * First party auth required
 */
Route::prefix('/minecraft')->group(function () {
    // Validate user's Minecraft username
    Route::post('validate', [\App\Http\Controllers\Services\MinecraftServiceController::class, 'verify']);
});
