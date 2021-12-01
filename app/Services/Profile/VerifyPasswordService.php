<?php

namespace App\Services\Profile;

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
     * @param $password
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request, $password): bool
    {
        $details = $request->session()->get('profile_verification_password');
        if (!$this->validateSessionDetails($details)) {
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
     * @return bool
     */
    protected function validateSessionDetails(array $details): bool
    {
        $validator = Validator::make($details, [
            'uuid' => 'required|string',
            'mc_username' => 'required',
            'password' => 'required|string',
            'password_expiry' => 'required'
        ]);

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
