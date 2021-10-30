<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------

| Endpoint: /*
|
*/

Route::post('/create-new-user', [\App\Http\Controllers\UserController::class, 'store']);
