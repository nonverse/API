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
Route::post('/validate-new-email', [\App\Http\Controllers\Validation\UserValidationController::class, 'validateNewEmail']);
Route::post('/validate-new-user', [\App\Http\Controllers\Validation\UserValidationController::class, 'validateNewUser']);

// Profile validation
Route::post('/validate-new-profile', [\App\Http\Controllers\Validation\ProfileValidationController::class, 'validateUsername']);
