<?php

namespace App\Services\Users;

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

    public function __construct(
        UserRepositoryInterface $repository,
        UserProfileRepositoryInterface $profileRepository
    )
    {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
    }

    //TODO Include logic to check for profile and delete other user data
    public function handle($uuid): bool
    {
        // Delete a user's profile if one exists
        if ($this->profileRepository->get($uuid)) {
            $this->profileRepository->delete($uuid);
        }

        // Delete a user's account store
        return $this->repository->delete($uuid);
    }
}
