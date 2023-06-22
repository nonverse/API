<?php

namespace App\Services\Phone;

use Exception;
use Twilio\Rest\Client;

class SendPhoneVerificationCodeService
{
    /**
     * @param string $phone
     * @return false[]|true[]|void
     */
    public function handle(string $phone)
    {
        /**
         * Try to send verification code using Twilio Verify
         */
        try {
            $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
            $verification = $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))->verifications->create($phone, 'sms');
        } catch (Exception $e) {
            return [
                'success' => false
            ];
        }

        /**
         * If verification status is pending, return success
         */
        if ($verification->status === 'pending') {
            return [
                'success' => true
            ];
        }
    }
}
