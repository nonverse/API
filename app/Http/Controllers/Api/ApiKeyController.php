<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\KeyCreationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiKeyController extends Controller
{
    /**
     * @var KeyCreationService
     */
    private $creationService;

    public function __construct(
        KeyCreationService $creationService
    )
    {
        $this->creationService = $creationService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'key_name' => 'required|string',
            'permissions' => 'required|array',
            'terms' => 'required'
        ]);

        // Fetch currently authenticated user
        $user = $request->user();

        // Ensure that terms and conditions have been agreed to
        if (!$request->input('terms')) {
            return response('Terms must be accepted', 400);
        }

        // Issue a new token for the user with the requested name and permissions
        $token = $this->creationService->handle($user->uuid, $request->all());

        return new JsonResponse([
            'data' => [
                'success' => true,
                'uuid' => $user->uuid,
                $token
            ]
        ]);
    }
}
