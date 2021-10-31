<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Users\UserCreationService;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @var UserCreationService
     */
    private $creationService;

    public function __construct(
        UserCreationService $creationService
    )
    {
        $this->creationService = $creationService;
    }

    /**
     * Check if a request email is already in use by a registered user
     *
     * @param Request $request
     *
     * @return Response
     */
    public function validateEmail(Request $request): Response
    {
        $rule = array('email' => 'unique:users,email');
        $validator = Validator::make($request->all(), $rule);

        return $validator->fails()
            ? response('email exists', 422)
            : response('email available', 200);
    }

    /**
     * Create a new user and store in database
     *
     * @param Request $request
     *
     * @return string
     */
    public function store(Request $request)
    {
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
