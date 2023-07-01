<?php

namespace App\Services\User;

use App\Notifications\VerifyEmail;
use Exception;
use Firebase\JWT\JWT;

trait VerifiesEmail
{
    /**
     * Send email verification to user's email address
     *
     * @return void
     * @throws Exception
     */
    public function sendEmailVerification(): void
    {
        $payload = [
            'iss' => env('APP_URL'),
            'sub' => $this->uuid,
            'aud' => env('APP_URL'),
            'exp' => time() + 24 * 60 * 60,
            'iat' => time(),
            'email' => $this->email
        ];

        /**
         * Encode JWT
         */
        $jwt = JWT::encode($payload, config('oauth.private_key'), 'RS256');

        /**
         * Try to send verification email to user
         */
        try {
            $this->notify(new VerifyEmail($this, $jwt));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}