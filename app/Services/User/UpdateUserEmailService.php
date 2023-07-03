<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Exception;

class UpdateUserEmailService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }


    /**
     * Update user's email address
     *
     * @param string $uuid
     * @param string $email
     * @return void
     * @throws Exception
     */
    public function handle(string $uuid, string $email): void
    {
        /**
         * Get user from database
         */
        try {
            $user = $this->repository->get($uuid);

            /**
             * Finish service if email is unchanged
             */
            if ($user->email === $email) {
                return;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        /**
         * Try to update user's email and send verification email
         */
        try {
            $userUpdated = $this->repository->update($uuid, [
                'email' => $email,
                'email_verified_at' => null
            ]);

            $userUpdated->sendEmailVerification();
        } catch (Exception $e) {
            $this->repository->update($uuid, [
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ]);

            throw new Exception($e->getMessage());
        }
    }
}
