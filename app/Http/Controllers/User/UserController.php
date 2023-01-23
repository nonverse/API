<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\User\UserCreationService;
use App\Services\User\UserDeletionService;
use App\Services\User\UserUpdateService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var UserCreationService
     */
    private UserCreationService $creationService;

    /**
     * @var UserUpdateService
     */
    private UserUpdateService $updateService;

    /**
     * @var UserDeletionService
     */
    private UserDeletionService $deletionService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserCreationService     $creationService,
        UserUpdateService       $updateService,
        UserDeletionService     $deletionService
    )
    {
        $this->repository = $userRepository;
        $this->creationService = $creationService;
        $this->updateService = $updateService;
        $this->deletionService = $deletionService;
    }

    /**
     * Get a user from database
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

    /**
     * Create a new user and persist to database
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'email' => 'required|email:rfc,dns|unique:users,email',
            'name_first' => 'required|string',
            'name_last' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:8|confirmed'
        ],
            [
                'email.unique' => 'This email is already in use by another account',
                'username.unique' => 'This username has already been taken',
            ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = $this->creationService->handle($validator->validated());

        return new JsonResponse([
            'success' => true,
            'data' => [
                'uuid' => $user->uuid
            ]
        ]);
    }

    /**
     * Update a user's data and persist to database
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Exception
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'email' => 'email:rfc,dns|unique:users,email',
            'username' => 'unique:users,username',
            'phone' => 'min:7|max:15',
            'password' => 'min:8'
        ],
            [
                'email.unique' => 'This email is already in use by another account',
                'username.unique' => 'This username has already been taken',
            ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = $this->updateService->handle($request->user()->uuid, $request->input());

        return new JsonResponse([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Delete a user from database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        if ($this->deletionService->handle('1296cd9a-4d3a-4c0b-8b61-a17f6568da3c')) {
            return new JsonResponse([
                'success' => true,
            ]);
        }

        return new JsonResponse([
            'success' => false
        ]);
    }
}
