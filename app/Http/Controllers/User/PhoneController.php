<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Phone\VerifyPhoneVerificationCodeService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * @var VerifyPhoneVerificationCodeService
     */
    private VerifyPhoneVerificationCodeService $verifyPhoneVerificationCodeService;

    public function __construct(
        UserRepositoryInterface            $userRepository,
        VerifyPhoneVerificationCodeService $verifyPhoneVerificationCodeService
    )
    {
        $this->userRepository = $userRepository;
        $this->verifyPhoneVerificationCodeService = $verifyPhoneVerificationCodeService;
    }

    /**
     * Handle user phone update request
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
            'phone' => 'required|min:7|max:15',
            'code' => 'required|min:6|max:6'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /**
         * Attempt to verify phone verification code
         */
        try {
            $check = $this->verifyPhoneVerificationCodeService->handle($request->input('phone'), $request->input('code'));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false
            ], 400);
        }

        /**
         * If verification code is incorrect, return HTTP 401
         */
        if (!$check['success']) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'code' => 'Incorrect verification code'
                ]
            ], 401);
        }

        /**
         * Attempt to update user's phone number and persist to database
         */
        try {
            $this->userRepository->update($request->user()->uuid, [
                'phone' => $request->input('phone'),
                'phone_verified_at' => CarbonImmutable::now()
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false
            ], 500);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
