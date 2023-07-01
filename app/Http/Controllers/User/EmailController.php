<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UpdateUserEmailService;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    /**
     * @var UpdateUserEmailService
     */
    private UpdateUserEmailService $updateUserEmailService;

    public function __construct(
        UpdateUserEmailService $updateUserEmailService
    )
    {
        $this->updateUserEmailService = $updateUserEmailService;
    }

    /**
     * Handle user email verification request
     *
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /**
         * Try to decode JWT
         */
        try {
            $jwt = (array)JWT::decode($request->input('token'), new Key(config('oauth.public_key'), 'RS256'));
        } catch (ExpiredException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'token' => 'Token has expired'
                ]
            ], 400);
        }

        /**
         * Get user from request
         */
        $user = $request->user();

        /**
         * Check if verification token UUID and E-Mail match that of the user's
         */
        if ($jwt['sub'] !== $user->uuid || $jwt['email'] !== $user->email) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Unauthorised'
            ], 401);
        }

        /**
         * Attempt to mark user's email as verified
         */
        try {
            $user->verifyEmail();
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false
            ], 500);
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Handle requests to update user's email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($request->user()->uuid, 'uuid')
            ]
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'error' => $validator->errors()
            ], 422);
        }

        /**
         * Attempt to update user's email
         */
        try {
            $this->updateUserEmailService->handle($request->user()->uuid, $request->input('email'));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
