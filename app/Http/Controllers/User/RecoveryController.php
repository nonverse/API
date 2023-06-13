<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\RecoveryRepositoryInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecoveryController extends Controller
{
    /**
     * @var RecoveryRepositoryInterface
     */
    private RecoveryRepositoryInterface $repository;

    public function __construct(
        RecoveryRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Get user's recovery details
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function get(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->repository->get($request->user()->uuid)
        ]);
    }
}
