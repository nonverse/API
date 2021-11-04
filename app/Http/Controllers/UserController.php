<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Users\UserUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Users\UserCreationService;
use Illuminate\Http\Response;
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

    public function __construct(
        UserCreationService     $creationService,
        UserUpdateService       $updateService,
        UserRepositoryInterface $repository
    )
    {
        $this->creationService = $creationService;
        $this->updateService = $updateService;
    }

    /**
     * Create a new user and store in database
     *
     * @param Request $request
     *
     * @return string
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'name_first' => 'required',
            'name_last' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $this->creationService->handle($request->all());

        return new JsonResponse([
            'data' => [
                'complete' => 'true',
                'uuid' => $user->uuid,
            ]
        ]);
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
