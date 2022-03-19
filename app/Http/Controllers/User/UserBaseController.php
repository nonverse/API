<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Users\UserDeletionService;
use App\Services\Users\UserUpdateService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
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
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class UserBaseController extends Controller
{
    /**
     * @var UserCreationService
     */
    private $creationService;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var InviteRepositoryInterface
     */
    private $inviteRepository;

    public function __construct(
        UserCreationService     $creationService,
        UserRepositoryInterface $repository,
        InviteRepositoryInterface $inviteRepository
    )
    {
        $this->creationService = $creationService;
        $this->repository = $repository;
        $this->inviteRepository = $inviteRepository;
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
            'activation_token' => 'required'
        ]);

        /*
         * Check if a valid activation token is present in the session
         */
        $details = $request->session()->get('activation_token');
        if (!$this->validateSessionDetails($details)) {
            return new JsonResponse([
                'errors' => [
                    'token' => 'Activation token has expired'
                ]
            ], 401);
        }

        /*
         * Check if the provided activation token is valid
         */
        if ($request->input('activation_token') !== $details['token_value']) {
            return new JsonResponse([
                'errors' => [
                    'token' => 'Invalid activation token'
                ]
            ], 401);
        }

        /*
         * Check if the email that is requesting to be registered
         * is the same as the one the activation token was issued for
         */
        if ($request->input('email') !== $details['email']) {
            return new JsonResponse([
                'errors' => [
                    'email' => 'Request data mismatch'
                ]
            ], 400);
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
        $this->inviteRepository->update($request->input('email'), [
            'claimed_by' => $user->uuid
        ]);

        // Login the user after registration
        $request->session()->regenerate();
        Auth::loginUsingId($user->uuid, false); //TODO Post registration login does not seem to be working
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

    /**
     * Get a user from database
     *
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $this->repository->get($request->user()->uuid);
    }

    /**
     * Verify that the session details are valid
     *
     * @param array $details
     * @return bool
     */
    protected function validateSessionDetails(array $details): bool
    {
        $validator = Validator::make($details, [
            'email' => 'required|email',
            'token_value' => 'required|string',
            'token_expiry' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        if (!$details['token_expiry'] instanceof CarbonInterface) {
            return false;
        }

        if ($details['token_expiry']->isBefore(CarbonImmutable::now())) {
            return false;
        }

        return true;
    }
}
