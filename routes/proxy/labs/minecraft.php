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
Route::get('/profile', [\App\Http\Controllers\Proxy\LabsProxyController::class, 'forward']);
