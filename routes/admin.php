<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API routes that can only be accessed by a session authenticated
| user with admin privileges via an app on the .nonverse.net domain
|--------------------------------------------------------------------------
| Endpoint: /admin
|
*/

/**
 * User Routes
 *
 * Endpoint: /admin/users
 */
Route::group(['prefix' => 'users'], function () {
    // Get all users
    Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'all']);
    // Get a user by UUID
    Route::get('/{uuid}', [\App\Http\Controllers\Admin\UserController::class, 'get']);

    // Administrative Routes
    Route::group(['middleware' => 'notself'], function () {
        Route::post('/{uuid}/suspend', [\App\Http\Controllers\Admin\UserAdministrationController::class, 'suspend']);
        Route::post('/{uuid}/ban', [\App\Http\Controllers\Admin\UserAdministrationController::class, 'ban']);
        Route::post('/{uuid}/pardon', [\App\Http\Controllers\Admin\UserAdministrationController::class, 'pardon']);
    });
});

// Get all profiles
Route::get('/profiles', [\App\Http\Controllers\Admin\ProfileController::class, 'all']);

/**
 * Invitation Routes
 *
 * Endpoint: /admin/invites
 */

Route::group(['prefix' => 'invites'], function () {
    // Get all invites
    Route::get('/', [\App\Http\Controllers\Admin\InviteController::class, 'all']);
    // Get all invite requests
    Route::get('/requests', [\App\Http\Controllers\Admin\InviteRequestController::class, 'all']);
    // Create new invite(s)
    Route::get('/create', [\App\Http\Controllers\Admin\InviteController::class, 'create']);
    // Delete invite
    Route::delete('/{email}', [\App\Http\Controllers\Admin\InviteController::class, 'delete']);
});
