<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserBaseController extends Controller
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
     * Get a user from database
     *
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $this->repository->get($request->user()->uuid);
    }
}
