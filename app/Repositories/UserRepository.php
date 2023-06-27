<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Return the model used in this repository
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    public function create(array $data, bool $force = false): User
    {
        $user = new User;
        ($force) ? $user->forceFill($data) : $user->fill($data);
        $user->save();

        return $user->fresh();
    }
}
