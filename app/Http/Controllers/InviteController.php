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
     * @var InviteRepositoryInterface
     */
    private $repository;

    /**
     * @var InviteCreationService
     */
    private $creationService;

    /**
     * @var InviteActivationService
     */
    private $activationService;

    public function __construct(
        InviteRepositoryInterface $repository,
        InviteCreationService     $creationService,
        InviteActivationService   $activationService
    )
    {
        $this->repository = $repository;
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
            'email' => 'required|email|unique:invites,email',
            'name' => 'required'
        ]);

        $invite = $this->creationService->handle($request, [
            'email' => $request->input('email'),
            'name' => $request->input('name')
        ]);

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

        $activation = $this->activationService->handle($request, $request->input('email'), $request->input('activation_key'));

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
                'activation_token' => $activation['token']
            ]
        ]);
    }

    public function all(Request $request)
    {
        return $this->repository->index();
    }

    /**
     * Delete an invite
     *
     * @param Request $request
     * @param $email
     * @return JsonResponse|void
     */
    public function delete(Request $request,$email)
    {
        $invite = $this->repository->get($email);
        // Ensure that the invite has not been claimed
        if ($invite->claimed_by) {
            return new JsonResponse([
                'errors' => [
                    'invite' => 'Cannot withdraw claimed invites'
                ],
            ], 400);
        }

        // Ensure that the currently logged in user created the invite
        if ($invite->invited_by !== $request->user()->uuid) {
            return new JsonResponse([
                'errors' => [
                    'invite' => 'Cannot withdraw invites created by other admins'
                ],
            ], 403);
        }

        if ($this->repository->delete($email)) {
            return new JsonResponse([
                'data' => [
                    'success' => true
                ]
            ]);
        }
    }
}
