<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Phone\VerifyPhoneVerificationCodeService;
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

    public function update(Request $request)
    {
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

        try {
            $check = $this->verifyPhoneVerificationCodeService->handle($request->input('phone'), $request->input('code'));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false
            ], 400);
        }

        if (!$check['success']) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'code' => 'Incorrect verification code'
                ]
            ], 401);
        }

        try {
            $this->userRepository->update($request->user()->uuid, [
                'phone' => $request->input('phone')
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
