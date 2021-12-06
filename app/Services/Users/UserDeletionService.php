<?php

namespace App\Services\Users;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Hashing\Hasher;
use PragmaRX\Google2FA\Google2FA;

class UserDeletionService
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

    //TODO Include logic to check for profile and delete other user data
    public function handle($uuid): bool
    {
        return $this->repository->delete($uuid);
    }
}
