<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Users\UserCreationService;

class UserController extends Controller
{
    /**
     * @var UserCreationService
     */
    private $creationService;

    public function __construct(
        UserCreationService $creationService
    ) {
        $this->creationService = $creationService;
    }

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
