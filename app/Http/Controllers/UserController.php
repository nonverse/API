<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\Users\UserCreationService;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserCreationService
     */
    private $creationService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserCreationService $creationService
    ) {
        $this->userRepository = $userRepository;
        $this->creationService = $creationService;
    }

    /**
     * Check if a request email is already in use by a registered user
     *
     * @param Request $request
     *
     * @return bool
     */
    public function validateEmail(Request $request): bool
    {
        $user =  $this->userRepository->get($request->input('email'));

        return (bool)$user;
    }

    /**
     * Create a new user and store in database
     *
     * @param Request $request
     *
     * @return string
     */
    public function store(Request $request) {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'name_first' => 'required',
            'name_last' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $this->creationService->handle($request->all());

        return 'Done';
    }
}
