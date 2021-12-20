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
     * @param $identifier
     * @return bool
     */
    public function handle(Request $request, $password, $identifier): bool
    {
        $details = $request->session()->get('one_time_password');
        if (!$this->validateSessionDetails($details, $identifier)) {
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
     * @param $identifier
     * @return bool
     */
    protected function validateSessionDetails(array $details, $identifier): bool
    {
        $validator = Validator::make($details, [
            'uuid' => 'required|string',
            'password' => 'required|string',
            'password_identifier' => 'required|string',
            'password_expiry' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        if ($identifier !== $details['password_identifier']) {
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
