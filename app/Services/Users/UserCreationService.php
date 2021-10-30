<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserCreationService
{
    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * UserCreationService Constructor
     */
    public function __construct(
        Hash $hash,
        UserRepositoryInterface $repository
    ) {
        $this->hash = $hash;
        $this->repository = $repository;
    }

    /**
     * Handle user registration
     *
     * @param array $data
     *
     * @return User
     */
    public function handle(array $data): User
    {
        $data['password'] = $this->hash->make($data['password']);
        $user = $this->repository->create(array_merge(
            $data,
            ['uuid' => Str::uuid()]
        ));

        return $user;
    }
}
