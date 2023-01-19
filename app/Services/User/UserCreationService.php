<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserCreationService
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
     * Create a new user and persist to database
     *
     * @param array $data
     * @return Model
     */
    public function handle(array $data): Model {

        /**
         * Generate a new UUID and password hash
         */
        $data = [
            ...$data,
            'uuid' => Str::uuid(),
            'password' => $this->hasher->make($data['password'])
        ];

        return $this->repository->create($data, true);
    }
}
