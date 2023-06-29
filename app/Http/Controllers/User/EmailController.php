<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UpdateUserEmailService;
use Exception;
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
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        //$request->fulfill();

        return new JsonResponse([
            'success' => true
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
