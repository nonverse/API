<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CORS protected auth routes
| These routes can only be accessed by apps on the .nonverse.net domain
|--------------------------------------------------------------------------

| Endpoint: /auth
|
*/
Route::group(['prefix' => 'verify'], function() {
    Route::post('validate-new-email', [\App\Http\Controllers\Auth\ValidationController::class, 'validateNewEmail']);
    Route::get('/verify-user-email', [\App\Http\Controllers\UserController::class, 'verifyEmail']);
});

Route::post('/create-new-user', [\App\Http\Controllers\UserController::class, 'store']);

