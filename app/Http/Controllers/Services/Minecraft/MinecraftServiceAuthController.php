<?php

namespace App\Http\Controllers\Services\Minecraft;

use App\Http\Controllers\Controller;
use App\Services\Services\Minecraft\MinecraftAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MinecraftServiceAuthController extends Controller
{
    /**
     * @var MinecraftAuthService
     */
    private MinecraftAuthService $minecraftAuthService;

    public function __construct(
        MinecraftAuthService $minecraftAuthService
    ) {
        $this->minecraftAuthService = $minecraftAuthService;
    }

    /**
     * Verify a user's Minecraft username
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateUsername(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'username' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$this->minecraftAuthService->validateUsername($request->input('username'))) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'username' => 'Profile not found with username ' . $request->input('username')
                ]
            ], 400);
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
//                'uuid' => $response['id']
            ]
        ]);
    }

    public function send(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
}
