<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\InviteRequestRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InviteRequestController extends Controller
{
    /**
     * @var InviteRequestRepositoryInterface
     */
    private $repository;

    public function __construct(
        InviteRequestRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Store an invite request in database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:invite_requests,email',
            'name' => 'required|string'
        ]);

        $inviteRequest = $this->repository->create([
            'email' => $request->input('email'),
            'name' => $request->input('name')
        ]);

        return new JsonResponse([
            'success' => true,
            'email' => $inviteRequest->email
        ]);
    }
}
