<?php

namespace App\Services\Phone;

use Exception;
use Twilio\Rest\Client;

class VerifyPhoneVerificationCodeService
{
    public function handle(string $phone, string $code)
    {
        /**
         * Try to verify code using Twilio Verify
         */
        try {
            $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
            $check = $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))->verificationChecks->create([
                'to' => $phone,
                'code' => $code
            ]);
        } catch (Exception $e) {
            return [
                'success' => false
            ];
        }

        /**
         * If verification check status is approved, return success
         */
        if ($check->status === 'approved') {
            return [
                'success' => true
            ];
        }
    }
}
