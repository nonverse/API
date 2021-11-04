<?php

namespace App\Services\Users;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserUpdateService
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
     * Handle user update
     *
     * @param $uuid
     * @param array $data
     * @return User|bool
     */
    public function handle($uuid, array $data) {
        if (Arr::has($data,'password')) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->update($uuid, $data);
    }

}
