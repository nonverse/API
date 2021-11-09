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

// Create user (No auth required)
Route::post('/', [\App\Http\Controllers\UserController::class, 'store']);

// Auth required
Route::group(['middleware' => 'auth'], function () {
    // Update a user
    Route::post('store', [\App\Http\Controllers\UserController::class, 'update']);
    // Delete a user's store
    Route::delete('store', [\App\Http\Controllers\UserController::class, 'delete']);
});

// Auth or API token required
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Get a user's details
    Route::get('store', [\App\Http\Controllers\UserController::class, 'get'])->middleware('ability:store:view');
});
