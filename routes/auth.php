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

Route::group(['middleware' => 'web'], function() {
    Route::post('/create-new-user', [\App\Http\Controllers\UserController::class, 'store']);

    Route::group(['prefix' => 'verify'], function() {
        Route::post('validate-user-email', [\App\Http\Controllers\UserController::class, 'validateEmail']);
    });
});

