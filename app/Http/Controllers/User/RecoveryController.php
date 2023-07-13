<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\Auth\RecoveryRepositoryInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecoveryController extends Controller
{
    /**
     * @var RecoveryRepositoryInterface
     */
    private RecoveryRepositoryInterface $repository;

    public function __construct(
        RecoveryRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Get user's recovery details
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function get(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->repository->get($request->user()->uuid)
        ]);
    }

    /**
     * Handle request to update user's recovery e-mail
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateEmail(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'owned' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /**
         * Get user from request
         */
        $user = $request->user();

        /**
         * Check that recovery email is different from account email
         */
        if ($request->input('email') === $user->email) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'email' => 'Recovery e-mail cannot be same as account e-mail'
                ]
            ], 422);
        }

        /**
         * Attempt to update user's recovery email
         */
        try {
            $user->updateRecoveryEmail($request->input('email'), $request->input('owned'));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        return new JsonResponse([
            'success' => true
        ]);
    }
}
