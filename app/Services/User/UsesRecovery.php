<?php

namespace App\Services\User;

use App\Models\Recovery;
use App\Notifications\VerifyEmail;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
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
}
