<?php

namespace App\Services\Admin;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;

class UserPardonService
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
     * Pardon a user on the network
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
                'error' => 'Cannot pardon an administrator account'
            ];
        }

        if (!$user->violations) {
            return [
                'success' => false,
                'error' => 'No violations on record'
            ];
        }

        // Pardon the user on the network
        $this->repository->update($uuid, [
            'violations' => null,
            'violation_ends_at' => null
        ]);

        //TODO Add logic to pardon profile on the network and game server(s)

        return [
            'success' => true
        ];
    }

}
