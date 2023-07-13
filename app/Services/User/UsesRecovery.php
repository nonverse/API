<?php

namespace App\Services\User;

use App\Models\Recovery;
use Carbon\CarbonImmutable;

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

        $recovery->isDirty() ?: $recovery->save();
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

        $recovery->isDirty() ?: $recovery->save();
    }
}
