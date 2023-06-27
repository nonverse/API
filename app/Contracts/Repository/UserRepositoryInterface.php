<?php

namespace App\Contracts\Repository;

use App\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function create(array $data, bool $force): User;
}
