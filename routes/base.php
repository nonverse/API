<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Base API Routes
|--------------------------------------------------------------------------
| Endpoint: /*
|
*/
Route::post('/request-invitation', [\App\Http\Controllers\InviteRequestController::class, 'store']);

