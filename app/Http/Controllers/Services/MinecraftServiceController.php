<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MinecraftServiceController extends Controller
{
    /**
     * Verify a user's Minecraft username
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
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

        /**
         * Check username with Mojang API
         */
        $response = json_decode(Http::get('https://api.mojang.com/users/profiles/minecraft/' . $request->input('username')), true);

        if (!array_key_exists('id', $response)) {
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
                'uuid' => $response['id']
            ]
        ]);
    }
}
