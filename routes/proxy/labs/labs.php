<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Labs API routes (Proxy)
|
| All routes should point to LabsProxyController::class, 'forward' and
| MUST be present in the Labs API /api
|--------------------------------------------------------------------------
|
| Endpoint: /labs
|
*/

Route::get('/', [\App\Http\Controllers\Proxy\LabsProxyController::class, 'forward']);

// Minecraft (Auth Required)
Route::middleware('auth:api')->prefix('minecraft')->group(base_path('routes/proxy/labs/minecraft.php'));

