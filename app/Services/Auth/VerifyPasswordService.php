<?php

namespace App\Services\Auth;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VerifyPasswordService
{
    /**
     * Verify a given One Time Password against hash
     *
     * @param Request $request
     * @param $password
     * @param bool $chat
     * @return bool
     */
    public function handle(Request $request, $password, bool $chat): bool
    {
        $details = $request->session()->get('one_time_password');
        if (!$this->validateSessionDetails($details, $chat)) {
            return false;
        }

        if (!Hash::check($password, $details['password'])) {
            return false;
        }

        return true;
    }

    /**
     * Verify that the session details are valid
     *
     * @param array $details
     * @param bool $chat
     * @return bool
     */
    protected function validateSessionDetails(array $details, bool $chat): bool
    {
        if ($chat) {
            $validator = Validator::make($details, [
                'uuid' => 'required|string',
                'mc_username' => 'required',
                'password' => 'required|string',
                'password_expiry' => 'required'
            ]);
        } else {
            $validator = Validator::make($details, [
                'uuid' => 'required|string',
                'password' => 'required|string',
                'password_expiry' => 'required',
            ]);
        }

        if (!$chat && array_key_exists('mc_username', $details)) {
            return false;
        }

        if ($validator->fails()) {
            return false;
        }

        if (!$details['password_expiry'] instanceof CarbonInterface) {
            return false;
        }

        if ($details['password_expiry']->isBefore(CarbonImmutable::now())) {
            return false;
        }

        return true;
    }
}
