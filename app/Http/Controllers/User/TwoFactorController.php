<?php

namespace App\Http\Controllers\User;

use App\Services\User\TwoStep\TwoFactorEnableService;
use App\Services\User\TwoStep\TwoFactorSetupService;
use Carbon\CarbonImmutable;
use Exception;
use http\Exception\RuntimeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;

class TwoFactorController
{
    /**
     * @var TwoFactorSetupService
     */
    private TwoFactorSetupService $setupService;

    /**
     * @var TwoFactorEnableService
     */
    private TwoFactorEnableService $enableService;

    public function __construct(
        TwoFactorSetupService   $setupService,
        TwoFactorEnableService  $enableService
    )
    {
        $this->setupService = $setupService;
        $this->enableService = $enableService;
    }

    /**
     * Get a user's 2FA setup data
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function get(Request $request): Response|JsonResponse
    {
        if ($request->user()->use_totp) {
            return response('TOTP is already enabled', 400);
        }

        return new JsonResponse([
            'data' => $this->setupService->handle($request->user())
        ]);
    }

    /**
     * Enable 2FA on a user's account
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function enable(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);

        return new JsonResponse([
            'data' => $this->enableService->handle($request->user(), $request->input('code'))
        ]);
    }

    /**
     * Disable 2FA on a user's account
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function disable(Request $request): Response|JsonResponse
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = $request->user();
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Incorrect password', 401);
        }

        try {
            $user->update([
                'use_totp' => false,
                'totp_authenticated_at' => CarbonImmutable::now()
            ]);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'success' => true
            ]
        ]);
    }
}
