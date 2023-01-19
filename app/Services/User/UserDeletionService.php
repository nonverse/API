<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Exception;

class UserDeletionService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->repository = $userRepository;
    }

    /**
     * Delete a user and purge all their associated data from database
     *
     * @param $uuid
     * @return Exception|bool
     */
    public function handle($uuid): Exception|bool
    {
        try {
            /*
             * Purge the user's account store
             */
            $user = $this->repository->delete($uuid, true);
        } catch (Exception $e) {
            return $e;
        }

        return $user;
    }
}
