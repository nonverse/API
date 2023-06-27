<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class UserUpdateService
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        Hasher                  $hasher
    )
    {
        $this->repository = $userRepository;
        $this->hasher = $hasher;
    }

    /**
     * @param $uuid
     * @param array $data
     * @return Exception|Model
     * @throws Exception
     */
    public function handle($uuid, array $data): Model|Exception
    {
        $user = $this->repository->get($uuid);

        /**
         * Format date of birth to Carbon time
         */
        if (Arr::has($data, 'dob')) {
            $data['dob'] = CarbonImmutable::parse($data['dob'])->format('Y-m-d');
        }

        /**
         * Update the user's data and persist to database
         */
        try {
            $user = $this->repository->update($uuid, $data);
        } catch (Exception $e) {
            return $e;
        }

        return $user;
    }
}
