<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Profile\SendPasswordService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Str;

class ProfileVerificationController extends Controller
{
    /**
     * @var Hasher
     */
    private $sendPasswordService;

    public function __construct(
        SendPasswordService $sendPasswordService
    )
    {
        $this->sendPasswordService = $sendPasswordService;
    }

    /**
     * Send a One Time Password to a user's in game chat
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function sendVerification(Request $request)
    {
        // validate request
        $request->validate([
            'mc_username' => 'required|unique:minecraft.profiles,mc_username',
            'terms' => 'required'
        ]);

        // Check if terms and conditions were agreed to
        if (!$request->input('terms')) {
            return response('Terms must be accepted', 422);
        }

        // Verify Minecraft username with Mojang API
        $response = Http::get('https://api.mojang.com/users/profiles/minecraft/' . $request->input('mc_username'));
        if ($response->status() !== 200) {
            return response('Invalid Minecraft username', 422);
        }

        if (!$this->sendPasswordService->handle($request, $response['name'])) {
            return response('Something went wrong', 500);
        }

        return new JsonResponse([
            'data' => [
                'mc_username' => $response['name'],
                'complete' => true
            ]
        ]);
    }
}
