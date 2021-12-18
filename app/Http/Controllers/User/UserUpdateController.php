<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Users\UserUpdateService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserUpdateController extends Controller
{
    private $updateService;

    public function __construct(
        UserUpdateService $updateService
    )
    {
        $this->updateService = $updateService;
    }

    /**
     * Update a user's password
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function updatePassword(Request $request)
    {
        // Validate request
        $request->validate([
            'password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        // Check if provided password is valid
        if (!Hash::check($request->input('password'), $request->user()->password)) {
            return response('Invalid password', 401);
        }

        $this->updateService->handle($request->user()->uuid, [
            'password' => $request->input('new_password')
        ]);

        return new JsonResponse([
            'data' => [
                'uuid' => $request->user()->uuid,
                'success' => true
            ]
        ]);
    }

    /**
     * Update a user's details (NOT PASSWORD)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name_first' => 'required',
            'name_last' => 'required',
            'email' => 'required'
        ]);

        $user = $this->updateService->handle($request->user()->uuid, $request->only(['name_first', 'name_last', 'email', 'phone', 'dob']));

        return new JsonResponse([
            'data' => [
                'uuid' => $request->user()->uuid,
                'success' => true,
                'user' => $user
            ]
        ]);
    }
}
