<?php

namespace App\Http\Controllers;

use App\Services\Phone\SendPhoneVerificationCodeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    /**
     * @var SendPhoneVerificationCodeService
     */
    private SendPhoneVerificationCodeService $sendPhoneVerificationCodeService;

    public function __construct(
        SendPhoneVerificationCodeService $sendPhoneVerificationCodeService
    )
    {
        $this->sendPhoneVerificationCodeService = $sendPhoneVerificationCodeService;
    }

    /**
     * Handle request to send verification code to phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'channel' => 'required',
            'phone' => 'required_without:email|min:7|max:15|prohibits:email',
            'email' => 'required_without:phone|email:rfc,dns|prohibits:phone'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /**
         * If verification delivery channel is phone...
         */
        if ($request->input('channel') == 'phone') {
            return $this->sendVerificationToPhone($request->input('phone'));
        }

        return new JsonResponse([
            'success' => false,
            'errors' => [
                'channel' => 'invalid delivery channel'
            ]
        ], 400);
    }

    /**
     * Send verification code to phone number
     *
     * @param string $phone
     * @return JsonResponse
     */
    protected function sendVerificationToPhone(string $phone): JsonResponse
    {
        /**
         * Attempt to send verification code to phone number
         */
        try {
            $this->sendPhoneVerificationCodeService->handle($phone);
        } catch (Exception) {
            return new JsonResponse([
                'success' => false
            ], 400);
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'phone' => $phone
            ]
        ]);
    }
}
