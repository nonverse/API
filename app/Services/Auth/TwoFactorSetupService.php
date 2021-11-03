<?php

namespace App\Services\Auth;

use App\Contracts\Repository\UserRepositoryInterface;

class TwoFactorSetupService
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

}
