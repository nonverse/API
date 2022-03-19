<?php

namespace App\Services\Users;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;

class UserDeletionService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var InviteRepositoryInterface
     */
    private $inviteRepository;

    public function __construct(
        UserRepositoryInterface        $repository,
        UserProfileRepositoryInterface $profileRepository,
        InviteRepositoryInterface      $inviteRepository
    )
    {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
        $this->inviteRepository = $inviteRepository;
    }

    //TODO Include logic to check for profile and delete other user data
    public function handle($uuid): bool
    {
        $user = $this->repository->get($uuid);

        // Delete a user's profile if one exists
        if ($this->profileRepository->get($uuid)) {
            $this->profileRepository->delete($uuid);
        }

        // Delete any invites used by the user
        if ($this->inviteRepository->get($user->email)) {
            $this->inviteRepository->delete($user->email);
        }

        // Delete a user's account store
        return $this->repository->delete($uuid);
    }
}
