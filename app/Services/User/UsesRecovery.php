<?php

namespace App\Services\User;

use App\Models\Recovery;
use App\Notifications\VerifyEmail;
use Carbon\CarbonImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Notification;

trait UsesRecovery
{
    /**
     * Update user's recovery e-mail
     *
     * @param string $email
     * @param bool $owned
     * @return void
     */
    public function updateRecoveryEmail(string $email, bool $owned): void
    {
        /**
         * Get recovery details for user
         */
        $recovery = Recovery::find($this->uuid);

        /**
         * Set user's recovery email
         */
        $recovery->email = $email;
        $recovery->email_belongs_to_user = (int)$owned;

        $recovery->save();

        /**
         * If the recovery email is owned by the user
         */
        if ($owned) {
            $payload = [
                'iss' => env('APP_URL'),
                'sub' => 'r-' . $this->uuid,
                'aud' => env('APP_URL'),
                'exp' => time() + 24 * 60 * 60,
                'iat' => time(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'email' => $email
            ];

            $jwt = JWT::encode($payload, config('oauth.private_key'), 'RS256');

            /**
             * Send verification e-mail with ip verification enabled
             */
            Notification::route('mail', $email)->notify(new VerifyEmail($this, $jwt));
        }
    }

    /**
     * Update user's recovery phone number
     *
     * @param string $phone
     * @return void
     */
    public function updateRecoveryPhone(string $phone): void
    {
        /**
         * Get recovery details for user
         */
        $recovery = Recovery::find($this->uuid);

        /**
         * Set user's recovery phone number
         */
        $recovery->phone = $phone;
        $recovery->phone_verified_at = CarbonImmutable::now();

        $recovery->save();
    }

    /**
     * Verify a user's recovery email address
     *
     * @param string $token
     * @return void
     * @throws Exception
     */
    public function verifyRecoveryEmail(string $token): void
    {
        /**
         * Decode JWT token
         */
        $decoded = (array)JWT::decode($token, new Key(config('oauth.public_key'), 'RS256'));

        /**
         * Get user's recovery
         */
        $recovery = Recovery::find($this->uuid);

        /**
         * Validate token
         */
        if (substr($decoded['sub'], 2) !== $this->uuid || $decoded['email'] !== $recovery->email) {
            throw new Exception('User data unauthorized');
        }

        //TODO check IP

        /**
         * Mark user's recovery email as verified
         */
        $recovery->email_verified_at = CarbonImmutable::now();
        $recovery->save();
    }
}
