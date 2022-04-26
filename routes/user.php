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

//Activate a user's account (No auth required)
Route::post('/activate', [\App\Http\Controllers\User\UserCreationController::class, 'activate']);

// Create user (No auth required)
Route::post('/', [\App\Http\Controllers\User\UserCreationController::class, 'store']);
Route::get('/', function () {
    return redirect('/user/store');
});

//Login
Route::get('/login', function () {
    return redirect('http://' . env('AUTH_SERVER') . '/login?host=' . $_SERVER['HTTP_HOST']);
})->name('login');

// Auth required
Route::group(['middleware' => 'auth'], function () {
    // Update a user's details
    Route::post('store', [\App\Http\Controllers\User\UserUpdateController::class, 'update']);
    // Update a user's password
    Route::post('store/password', [\App\Http\Controllers\User\UserUpdateController::class, 'updatePassword']);
    // Initialise deletion of a user's account
    Route::post('store/delete', [\App\Http\Controllers\User\UserDeletionController::class, 'initialise']);
    // Delete a user's account
    Route::delete('store', [\App\Http\Controllers\User\UserDeletionController::class, 'delete']);

    // Update a user's preferences
    Route::post('/preferences', [\App\Http\Controllers\User\UserPreferenceController::class, 'update']);

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
    Route::get('store', [\App\Http\Controllers\User\UserBaseController::class, 'get'])->middleware('ability:store:view');
    // Get a user's network preferences
    Route::get('preferences', [\App\Http\Controllers\User\UserPreferenceController::class, 'all'])->middleware('ability:preferences:view');
});
