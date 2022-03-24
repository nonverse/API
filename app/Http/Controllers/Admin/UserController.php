<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;

class UserController extends Controller
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
     * Get list of all users in database
     *
     * @return mixed
     */
    public function all()
    {
        return $this->repository->index();
    }

    public function get($uuid)
    {
        return $this->repository->get($uuid);
    }
}
