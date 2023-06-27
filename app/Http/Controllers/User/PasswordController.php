<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\UpdateUserPasswordService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    /**
     * @var UpdateUserPasswordService
     */
    private UpdateUserPasswordService $updateUserPasswordService;

    public function __construct(
        UpdateUserPasswordService $updateUserPasswordService
    )
    {
        $this->updateUserPasswordService = $updateUserPasswordService;
    }

    /**
     * Handle request to update user's password
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
            'password' => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        $user = $request->user();
        $restricted = strtolower($user->name_first . $user->name_last . $user->username . $user->email);

        /**
         * Check if password contains user's personal information
         */
        if (str_contains(strtolower($request->input('password')), $restricted)) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'password' => 'password cannot contain personal info'
                ]
            ], 422);
        }

        /**
         * Attempt to update user's password
         */
        try {
            $this->updateUserPasswordService->handle($user->uuid, $request->input('password'));
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
