<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Services\Base\InviteActivationService;
use App\Services\Base\InviteCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * @var InviteCreationService
     */
    private $creationService;

    /**
     * @var InviteActivationService
     */
    private $activationService;

    public function __construct(
        InviteCreationService   $creationService,
        InviteActivationService $activationService
    )
    {
        $this->creationService = $creationService;
        $this->activationService = $activationService;
    }

    /**
     * Invite a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        $invite = $this->creationService->handle($request, $request->input('email'));

        return new JsonResponse([
            'data' => [
                'success' => true,
                'email' => $invite->email
            ]
        ]);
    }

    /**
     * Activate and initialise a new user's account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function activate(Request $request): JsonResponse
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
                'success' => true
            ]
        ]);
    }
}
