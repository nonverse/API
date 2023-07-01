<?php

namespace App\Services\User;

use App\Notifications\VerifyEmail;
use Carbon\CarbonImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;

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

    /**
     * Verify a user's email
     *
     * @throws Exception
     */
    public function verifyEmail(string $token): void
    {
        /**
         * Decode verification token
         */
        $decoded = (array)JWT::decode($token, new Key(config('oauth.public_key'), 'RS256'));

        /**
         * Check if verification token UUID and E-Mail match that of the user's
         */
        if ($decoded['sub'] !== $this->uuid || $decoded['email'] !== $this->email) {
            throw new Exception('User data unauthorized');
        }

        /**
         * Mark e-mail as verified
         */
        $this->update([
            'email_verified_at' => CarbonImmutable::now()
        ]);
    }
}
