<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\OAuth2\UserOAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * @var UserOAuthService
     */
    private UserOAuthService $userOAuthService;

    public function __construct(
        userOAuthService $userOAuthService
    )
    {
        $this->userOAuthService = $userOAuthService;
    }

    /**
     * Handle requests to get all the services linked to a user's account
     *
     * @param Request $request
     * @return JsonResponse|array
     * @throws Exception
     */
    public function get(Request $request): JsonResponse|array
    {
        $oauthServices = $this->userOAuthService->authorizedClients($request->user());

        return new JsonResponse([
            'data' => [
                ...$oauthServices
                //TODO Return other services
            ]
        ]);
    }
}
