<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;

class UpdateUserPasswordService
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
        UserRepositoryInterface $repository,
        Hasher                  $hasher
    )
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    /**
     * @param string $uuid
     * @param string $password
     * @return Model
     * @throws Exception
     */
    public function handle(string $uuid, string $password): Model
    {
        return $this->repository->update($uuid, [
            'password' => $this->hasher->make($password)
        ], true);

    }
}
