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
     * Verify if a incoming request is linked to an existing account
     * and return the user it belongs to
     *
     * @param Request $request
     * @return JsonResponse|int
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = $this->repository->get($request->input('email'));

        if (!$user) {
            return Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse([
            'data' => [
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last
            ]
        ]);
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
