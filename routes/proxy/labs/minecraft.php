<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Labs Minecraft API routes
|--------------------------------------------------------------------------
|
| Endpoint: /labs/minecraft
|
*/

/**
 * Authentication required
 */
Route::middleware('auth:api')->prefix('profile')->group(function() {
    Route::post('/', [\App\Http\Controllers\Proxy\LabsProxyController::class, 'forward'])->middleware('scope:labs.*');
    Route::get('/', [\App\Http\Controllers\Proxy\LabsProxyController::class, 'forward'])->middleware('scope:labs.mc.profile.read');

    Route::post('/send-verification',[\App\Http\Controllers\Proxy\LabsProxyController::class, 'forward'])->middleware('scope:labs.*');
});
