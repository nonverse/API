<?php

namespace App\Http\Controllers\Profile;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Profile\ProfileCreationService;
use App\Services\Profile\VerifyPasswordService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

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

        Http::withToken(env('PANEL_ADMIN_KEY'))->post('https://' . env('PANEL_APP') . '/api/client/servers/' . env('MINECRAFT_LOBBY_SERVER') . '/command', [
            'command' => 'tellraw @p ["",{"text":"Your profile has successfully been linked to a Nonverse account with email ","bold":true,"color":"green"},{"text":"'. $request->user()->email .'","color":"gold"}]'
        ]);

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'mc_uuid' => $profile['mc_uuid']
            ]
        ]);
    }

    public function get(Request $request)
    {
        return $this->repository->get($request->user()->uuid);
    }
}
