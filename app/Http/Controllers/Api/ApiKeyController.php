<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\KeyCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key_name' => 'required|string',
            'permissions' => 'required|array'
        ]);

        $token = $this->creationService->handle($request->user()->uuid, $request->all());

        return new JsonResponse([
            'data' => [
                'success' => true,
                'uuid' => $request->user()->uuid,
                $token
            ]
        ]);
    }
}
