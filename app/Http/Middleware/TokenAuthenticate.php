<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TokenAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('API_AUTHENTICATION');
        $uuid = $request->header('CLIENT_IDENTIFIER');
        $response = Http::post('http://' . env('AUTH_SERVER') . '/api/authenticate', [
            'uuid' => $uuid,
            'token' => $token
        ]);

        if ($response->failed() || !$response['data']['authenticated']) {
            return new JsonResponse([
                'errors' => [
                    'token' => 'Invalid API access token'
                ]
            ], 401);
        }

        Auth::onceUsingId($response['data']['uuid']);

        return $next($request);
    }
}
