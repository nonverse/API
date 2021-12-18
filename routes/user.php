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
Route::post('/', [\App\Http\Controllers\User\UserController::class, 'store']);
Route::get('/', function () {
    return redirect('/user/store');
});

// Auth required
Route::group(['middleware' => 'auth'], function () {
    // Update a user's details
    Route::post('store', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
    // Update a user's password
    Route::post('store/password', [\App\Http\Controllers\User\UserUpdateController::class, 'updatePassword']);
    // Delete a user's store
    Route::delete('store', [\App\Http\Controllers\User\UserController::class, 'delete']);

    // (Re)send a user's email verification link
    Route::post('/email', [\App\Http\Controllers\User\EmailVerificationController::class, 'resend'])->name('verification.send');
    // Verify a user's email address
    Route::get('/email/{id}/{hash}', [\App\Http\Controllers\User\EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

    // Create a new API Key for a user
    Route::post('/key', [\App\Http\Controllers\Api\ApiKeyController::class, 'store']);
    // Delete a user's API Key
    Route::delete('/key', [\App\Http\Controllers\Api\ApiKeyController::class, 'delete']);
    // Get a list of all API Keys for a user
    Route::get('/keys', [\App\Http\Controllers\Api\ApiKeyController::class, 'get']);
});

// Auth or API token required
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Get a user's details
    Route::get('store', [\App\Http\Controllers\User\UserController::class, 'get'])->middleware('ability:store:view');
});
