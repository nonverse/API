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
Route::get('/', [\App\Http\Controllers\User\ServicesController::class, 'get'])->middleware('scope:user.services.read');
