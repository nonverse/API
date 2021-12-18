<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Users\UserDeletionService;
use App\Services\Users\UserUpdateService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Users\UserCreationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

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
     * @var UserDeletionService
     */
    private $deletionService;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var Google2FA
     */
    private $google2FA;

    public function __construct(
        UserCreationService     $creationService,
        UserUpdateService       $updateService,
        UserDeletionService     $deletionService,
        UserRepositoryInterface $repository,
        Hasher                  $hasher,
        Encrypter               $encrypter,
        Google2FA               $google2FA
    )
    {
        $this->creationService = $creationService;
        $this->updateService = $updateService;
        $this->deletionService = $deletionService;
        $this->repository = $repository;
        $this->hasher = $hasher;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
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

        // Create new user and persist to database
        $user = $this->creationService->handle($request->all());

        // Login the user after registration
        $request->session()->regenerate();
        Auth::loginUsingId($user->uuid, false);
        $cookie = cookie('uuid', $user->uuid, 2628000);

        // Dispatch user registration event upon successful registration
        event(new Registered($user));

        return response()->json([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid,
            ]
        ])->cookie($cookie);
    }

    public function get(Request $request)
    {
        return $this->repository->get($request->user()->uuid);
    }

    /**
     * Delete a user's account details from database
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    function delete(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if the user has provided a correct password before continuing
        if (!$this->hasher->check($request->input('password'), $user->password)) {
            return new JsonResponse([
                'data' => [
                    'success' => false,
                ],
                'errors' => 'Password incorrect',
            ]);
        }

        // If the user has 2FA enabled, check if they have provided a correct authorisation code
        if ($user->use_totp) {
            $secret = $this->encrypter->decrypt($user->totp_secret);
            if (!$request->input('code') || !$this->google2FA->verifyKey($secret, $request->input('code'))) {
                return new JsonResponse([
                    'data' => [
                        'success' => false,
                    ],
                    'errors' => 'Incorrect authorisation code',
                ]);
            }
        }

        // Attempt to purge a user's account data
        return new JsonResponse([
            'data' => [
                'success' => $this->deletionService->handle($user->uuid)
            ]
        ]);
    }
}
