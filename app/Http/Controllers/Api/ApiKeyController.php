<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\ApiKeyRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Api\KeyCreationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ApiKeyController extends Controller
{
    /**
     * @var KeyCreationService
     */
    private $creationService;

    /**
     * @var ApiKeyRepositoryInterface
     */
    private $repository;

    public function __construct(
        KeyCreationService        $creationService,
        ApiKeyRepositoryInterface $repository
    )
    {
        $this->creationService = $creationService;
        $this->repository = $repository;
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

        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        // Ensure that terms and conditions have been agreed to
        if (!$request->input('terms')) {
            return response('Terms must be accepted', 400);
        }

        // Issue a new token for the user with the requested name and permissions
        if (!$this->creationService->handle($user->uuid, $request->all())) {
            return response('Something went wrong', 500);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
                'uuid' => $user->uuid,
            ]
        ]);
    }

    public function get(Request $request): object
    {
        return $this->repository->get($request->user());
    }
}
