<?php

namespace App\Services\Admin;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Carbon\CarbonImmutable;

class UserBanService
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
     * Ban a user on the network
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

        // Verify that the use is not an admin
        if ($user->admin) {
            return [
                'success' => false,
                'error' => 'Cannot ban an administrator account'
            ];
        }

        // Ban the user on the network
        $this->repository->update($uuid, [
            'violations' => 'banned',
            'violation_ends_at' => null
        ]);

        //TODO Add logic to ban profile on the network and game server(s)

        return [
            'success' => true
        ];
    }
}
