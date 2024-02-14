<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;

class ApplicationProxyService
{
    public function createSignedToken(string $target, User $user = null): string
    {
        $payload = [
            'sub' => $user?->uuid,
            'iss' => env('APP_URL'),
            'aud' => 'https://' . $target . '.nonverse.test/',
            'iat' => time(),
            'exp' => time() + 60
        ];

        /**
         * Create new access token
         */
        return JWT::encode($payload, config('api.private_key'), 'RS256');
    }
}
