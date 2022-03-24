<?php

namespace App\Services\Admin;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class UserSuspensionService
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
     * Suspend a user on the network
     *
     * @param $uuid
     * @param $period
     * @return array|bool[]
     */
    public function handle($uuid, $period): array
    {

        /**
         * @var User
         */
        $user = $this->repository->get($uuid);

        // Verify that the use is not an admin
        if ($user->admin) {
            return [
                'success' => false,
                'error' => 'Cannot suspend an administrator account'
            ];
        }

        // Verify that the user does not have any existing violations
        if ($user->violations) {
            return [
                'success' => false,
                'error' => 'Cannot suspend an account with existing violations'
            ];
        }

        // Suspend the user on the network
        $until = CarbonImmutable::now()->addDays($period);
        $this->repository->update($uuid, [
            'violations' => 'suspended',
            'violation_ends_at' => $until
        ]);

        //TODO Add logic to suspend profile on the network and game server(s)

        return [
            'success' => true,
            'violation_ends_at' => $until
        ];
    }
}
