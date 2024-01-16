<?php

namespace App\Http\Controllers;

use App\Services\Auth\OneTimePasswordService;
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

    /**
     * @var OneTimePasswordService
     */
    private OneTimePasswordService $oneTimePasswordService;

    public function __construct(
        SendPhoneVerificationCodeService $sendPhoneVerificationCodeService,
        OneTimePasswordService           $oneTimePasswordService
    )
    {
        $this->sendPhoneVerificationCodeService = $sendPhoneVerificationCodeService;
        $this->oneTimePasswordService = $oneTimePasswordService;
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
            'target' => 'required'
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
            // Validate target
            return new JsonResponse($this->sendVerificationToPhone($request->input('target')));
        }

        if (in_array($request->input('channel'), config('auth.one_time_passwords.channels'))) {
            return new JsonResponse($this->oneTimePasswordService->send($request->user(), $request->input('channel'), $request->input('action_id')));
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
