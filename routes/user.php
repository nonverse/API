<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API routes that can only be accessed by a session authenticated
| user via an app on the .nonverse.net domain
|--------------------------------------------------------------------------
| Endpoint: /user
|
*/

// Create user
Route::post('create-new-user', [\App\Http\Controllers\UserController::class, 'store']);

// Update a user
Route::post('update', [\App\Http\Controllers\UserController::class, 'update']);
