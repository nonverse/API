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

// Invite a new user
Route::get('/invite-new-user', [\App\Http\Controllers\InviteController::class, 'create']);
