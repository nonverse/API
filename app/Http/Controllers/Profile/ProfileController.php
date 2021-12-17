<?php

namespace App\Http\Controllers\Profile;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Profile\ProfileCreationService;
use App\Services\Profile\VerifyPasswordService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private $repository;

    /**
     * @var VerifyPasswordService
     */
    private $verifyPasswordService;

    /**
     * @var ProfileCreationService
     */
    private $creationService;

    public function __construct
    (
        UserProfileRepositoryInterface $repository,
        VerifyPasswordService          $verifyPasswordService,
        ProfileCreationService         $creationService
    )
    {
        $this->repository = $repository;
        $this->verifyPasswordService = $verifyPasswordService;
        $this->creationService = $creationService;
    }

    /**
     * Create a new user profile
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'mc_username' => 'required|unique:minecraft.profiles,mc_username',
            'password' => 'required|string',
            'terms' => 'required'
        ]);

        // Check if Terms and Conditions were accepted
        if (!$request->input('terms')) {
            return response('Terms must be accepted', 422);
        }

        if (!$request->session()->get('profile_verification_password')) {
            return response('Invalid password', 401);
        }

        // Check that the requested Minecraft username is that same one that the OTP was sent to
        if ($request->input('mc_username') !== $request->session()->get('profile_verification_password')['mc_username']) {
            return response('Request data mismatch', 400);
        }

        // Check if a valid OTP was provided
        if (!$this->verifyPasswordService->handle($request, $request->input('password'))) {
            return response('Invalid password', 401);
        }

        // Create a new profile
        $profile = $this->creationService->handle($request->user()->uuid, $request->input('mc_username'));

        // Remove OTP session store
        $request->session()->forget('profile_verification_password');

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'profile' => $profile
            ]
        ]);
    }

    /**
     * Get a user's profile
     *
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $this->repository->get($request->user()->uuid);
    }

    /**
     * Delete a user's profile
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function delete(Request $request)
    {
        $user = $request->user();
        // Verify if the user has provided a valid password
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        // Attempt to delete the user's profile
        try {
            $this->repository->delete($user->uuid);
        } catch (ModelNotFoundException $e) {
            return response('Something went wrong', 500);
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'success' => true
            ]
        ]);
    }
}
