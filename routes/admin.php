<?php

use Illuminate\Http\Request;
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
Route::group(['prefix' => 'users'], function() {
    // Get all users
    Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'all']);
    // Get a user by UUID
    Route::get('/{uuid}', [\App\Http\Controllers\Admin\UserController::class, 'get']);
});

/**
 * Invitation Routes
 *
 * Endpoint: /admin/invites
 */

Route::group(['prefix' => 'invites'], function() {
    // Get all invites
    Route::get('/', [\App\Http\Controllers\InviteController::class, 'all']);
    // Create new invite(s)
    Route::get('/create', [\App\Http\Controllers\InviteController::class, 'create']);
    // Delete invite
    Route::delete('/{email}', [\App\Http\Controllers\InviteController::class, 'delete']);
});
