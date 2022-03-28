<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repository\InviteRequestRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InviteRequestController extends Controller
{
    /**
     * @var InviteRequestRepositoryInterface
     */
    private $repository;

    public function __construct(
        InviteRequestRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function all() {
        return $this->repository->index();
    }
}
