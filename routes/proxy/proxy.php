<?php

use Illuminate\Support\Facades\Route;

/**
 * Proxy routes
 * These routes are used to send authorized requests to backend servers
 * Each route in the backend server should have a corresponding route here
 * with the appropriate authentication/authorization middlewares defined
 *
 * Endpoint: /
 */

// Labs
Route::prefix('labs')->group(base_path('routes/proxy/labs/labs.php'));
