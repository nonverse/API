<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Auth\SendPasswordService;
use App\Services\Auth\VerifyPasswordService;
use App\Services\Users\UserDeletionService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class UserDeletionController extends \App\Http\Controllers\Controller
{
    /**
     * @var UserDeletionService
     */
    private $deletionService;

    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var SendPasswordService
     */
    private $sendPasswordService;

    /**
     * @var VerifyPasswordService
     */
    private $verifyPasswordService;

    public function __construct(
        UserDeletionService     $deletionService,
        Google2FA               $google2FA,
        Encrypter               $encrypter,
        SendPasswordService     $sendPasswordService,
        VerifyPasswordService   $verifyPasswordService
    )
    {
        $this->deletionService = $deletionService;
        $this->google2FA = $google2FA;
        $this->encrypter = $encrypter;
        $this->sendPasswordService = $sendPasswordService;
        $this->verifyPasswordService = $verifyPasswordService;
    }

    /**
     * Verify a user's password and send email containing OTP to delete account
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function initialise(Request $request)
    {
        // Fetch user from session
        $user = $request->user();

        // Verify password
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        // If user has 2FA enabled, verify code
        if ($user->use_totp) {
            if (!$this->google2FA->verifyKey($this->encrypter->decrypt($user->totp_secret), $request->input('code'))) {
                return response('Invalid code', 401);
            }
        }

        // Create a new termination token and one time password
        $token = Str::random(64);

        // Create a new token array and store in session alongside otp hash
        $request->session()->put('user_termination_token', [
            'uuid' => $user->uuid,
            'token_value' => $this->encrypter->encrypt($token),
            'token_expiry' => CarbonImmutable::now()->addMinutes(5),
        ]);

        // Attempt to send an email containing OTP
        if (!$this->sendPasswordService->email($request, 'user_termination_password')) {
            $request->session()->forget('user_termination_token');
            return response('Unable to send email', 500);
        }

        // Return response containing raw token value and user UUID
        return new JsonResponse([
            'data' => [
                'success' => true,
                'uuid' => $user->uuid,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Delete a user's account and details from database
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function delete(Request $request)
    {
        // Fetch user and token array from session
        $user = $request->user();
        $details = $request->session()->get('user_termination_token');

        // Validate token array
        if (!$this->validateSessionDetails($details)) {
            return response('Termination token has expired', 400);
        }

        // Validate request uuid
        if ($user->uuid !== $details['uuid']) {
            return response('Request data mismatch', 400);
        }

        // Validate termination token
        if ($request->input('termination_token') !== $this->encrypter->decrypt($details['token_value'])) {
            return response('Invalid termination token', 400);
        }

        // Validate one time password
        if (!$this->verifyPasswordService->handle($request, $request->input('otp'), 'user_termination_password')) {
            return response('Invalid one time password', 401);
        }

        // Clear session stores
        $request->session()->forget('user_termination_token');
        $request->session()->forget('one_time_password');

        // Attempt to delete user from database and return response
        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'complete' => $this->deletionService->handle($user->uuid)
            ]
        ]);
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
            'token_value' => 'required|string',
            'token_expiry' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        if (!$details['token_expiry'] instanceof CarbonInterface) {
            return false;
        }

        if ($details['token_expiry']->isBefore(CarbonImmutable::now())) {
            return false;
        }

        return true;
    }
}
