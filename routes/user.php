<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Endpoint: /user
|
*/

// Create new user
Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store'])->middleware('auth.authkey');

/**
 * Authentication required
 */
Route::group(['middleware' => 'auth:api'], function() {
    // Get user store
    Route::get('/store', [\App\Http\Controllers\User\UserController::class, 'get']);
    // Update user store
    Route::post('/store', [\App\Http\Controllers\User\UserController::class, 'update']);
    // Delete user store
    Route::delete('/store',  [\App\Http\Controllers\User\UserController::class, 'delete']);
});
