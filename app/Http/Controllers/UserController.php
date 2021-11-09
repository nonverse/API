<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Users\UserUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Users\UserCreationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * @var UserCreationService
     */
    private $creationService;

    /**
     * @var UserUpdateService
     */
    private $updateService;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserCreationService     $creationService,
        UserUpdateService       $updateService,
        UserRepositoryInterface $repository
    )
    {
        $this->creationService = $creationService;
        $this->updateService = $updateService;
        $this->repository = $repository;
    }

    /**
     * Create a new user and store in database
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'name_first' => 'required',
            'name_last' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $this->creationService->handle($request->all());

        $request->session()->regenerate();
        Auth::loginUsingId($user->uuid, false);
        return new JsonResponse([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid,
            ]
        ]);
    }

    public function get(Request $request) {
        return $this->repository->get($request->user()->uuid);
    }

    public function update(Request $request)
    {
        $data = $request->except([
            'use_totp',
            'totp_secret',
            'admin'
        ]);

        return $this->updateService->handle($request->user()->uuid, $data);
    }
}
