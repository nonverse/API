<?php

namespace App\Repositories;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Primary key that should be used when searching this repository
     *
     * @var string
     */
    protected string $primaryKey = 'uuid';

    /**
     * Return the model used in this repository
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }
}
