<?php

namespace App\Services\Admin;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;

class UserUpgradeService
{

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Make a user an Administrator on the network
     *
     * @param $uuid
     * @return array|bool[]
     */
    public function handle($uuid): array
    {

        /**
         * @var User
         */
        $user = $this->repository->get($uuid);

        // Verify that the user is not already an admin
        if ($user->admin) {
            return [
                'success' => false,
                'error' => 'User is already an administrator'
            ];
        }

        // Upgrade the user's account to Administrator
        $this->repository->update($uuid, [
            'admin' => 1
        ]);

        return [
            'success' => true
        ];
    }
}
