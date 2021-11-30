<?php

namespace App\Http\Controllers\Validation;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfileValidationController
{

    /**
     * Verify a given Minecraft username with Mojang API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateUsername(Request $request): JsonResponse
    {
        // Ensure that the user profile requested is on not already linked to another account
        $request->validate([
            'mc_username' => 'required|unique:minecraft.profiles,mc_username'
        ]);

        try {
            // Make a request to Mojang API
            $response = Http::get('https://api.mojang.com/users/profiles/minecraft/' . $request->input('mc_username'));
        } catch (Exception $e) {
            // If the Mojang server's are down or have an error return an error response with status 500 (Server Error)
            return new JsonResponse([
                'data' => [
                    'mc_username' => $request->input('mc_username'),
                ],
                'error' => 'Unable to contact Mojang API'
            ], 500);
        }

        // If a valid profile is not found, the Mojang API will return a response with status 204 (No Content)
        // This section converts that into a readable error response with status 404 (Not Found)
        if ($response->status() === 204) {
            return new JsonResponse([
                'data' => [
                    'mc_username' => $request->input('mc_username')
                ],
                'error' => 'Unable to find valid user profile'
            ], 404);
        }

        // If a valid user is found, return a JSON response containing the user's current Minecraft username and UUID
        return new JsonResponse([
            'data' => [
                'mc_username' => $response['name'],
                'mc_uuid' => $response['id']
            ]
        ]);
    }

}
