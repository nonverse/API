<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
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
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserCreationService     $creationService,
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
        $this->creationService = $creationService;
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
}
