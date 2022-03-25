<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repository\UserProfileRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @var UserProfileRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserProfileRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Get list of all profiles in database
     *
     * @return mixed
     */
    public function all() {
        return $this->repository->index();
    }
}
