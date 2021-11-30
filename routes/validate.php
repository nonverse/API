<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CORS protected input validation routes
| These routes can only be accessed by apps on the .nonverse.net domain
|--------------------------------------------------------------------------
| Endpoint: /validator
|
*/

// User validation
Route::group(['prefix' => 'user'], function() {
    Route::post('/', [\App\Http\Controllers\Validation\UserValidationController::class, 'validateNewUser']);
    Route::post('/email', [\App\Http\Controllers\Validation\UserValidationController::class, 'validateNewEmail']);
});

// Profile validation
Route::group(['prefix' => 'profile'], function() {
    Route::post('/', [\App\Http\Controllers\Validation\ProfileValidationController::class, 'validateUsername']);
});
