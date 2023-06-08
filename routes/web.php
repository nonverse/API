<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return new JsonResponse([
        'application_name' => env('APP_NAME'),
        'identifier' => env('APP_IDENTIFIER'),
        'version' => env('APP_VERSION'),
        'current_user' => null
    ]);
});

Route::get('http-401-unauthorized', function () {
    return new JsonResponse([
        'error' => 'unauthorized'
    ], 401);
})->name('login');
