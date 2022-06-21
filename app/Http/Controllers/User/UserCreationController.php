<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Base\InviteActivationService;
use App\Services\Users\UserCreationService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\Translation\t;

class UserCreationController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var InviteRepositoryInterface
     */
    private $inviteRepository;

    /**
     * @var InviteActivationService
     */

    private $activationService;

    /**
     * @var UserCreationService
     */
    private $creationService;

    public function __construct(
        UserRepositoryInterface   $repository,
        InviteRepositoryInterface $inviteRepository,
        InviteActivationService   $activationService,
        UserCreationService       $creationService
    )
    {
        $this->repository = $repository;
        $this->inviteRepository = $inviteRepository;
        $this->activationService = $activationService;
        $this->creationService = $creationService;
    }

    /**
     * Verify a user's activation key
     * This does not consume the key
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'activation_key' => 'required'
        ]);

        $activation = $this->activationService->handle($request->input('email'), $request->input('activation_key'));

        if (!$activation['success']) {
            return new JsonResponse([
                'errors' => [
                    'activation_key' => $activation['error']
                ]
            ], 401);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
            ]
        ]);
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
            'activation_key' => 'required'
        ]);

        /*
         * Check the user's activation key again before continuing registration
         */
        $activation = $this->activationService->handle($request->input('email'), $request->input('activation_key'));

        if (!$activation['success']) {
            return new JsonResponse([
                'errors' => [
                    'activation_key' => $activation['error']
                ]
            ], 401);
        }

        // Check if a user's password contains any part of their name(s)
        $password = $request->input('password');
        if (str_contains($password, $request->input('name_first')) || str_contains($password, $request->input('name_last'))) {

            return new JsonResponse([
                'errors' => [
                    'password' => 'Password cannot contain your name'
                ]
            ], 422);
        }

        // Create new user and persist to database
        $user = $this->creationService->handle($request->all());
        // Mark the user's activation key as used
        $this->inviteRepository->update($request->input('email'), [
            'claimed_by' => $user->uuid
        ]);

        // Dispatch user registration event upon successful registration
        event(new Registered($user));

        return response()->json([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid,
            ]
        ]);
    }
}
