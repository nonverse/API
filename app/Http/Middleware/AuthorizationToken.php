<?php

namespace App\Http\Middleware;

use App\Contracts\Repository\Auth\AuthorizationTokenRepositoryInterface;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationToken
{
    /**
     * @var AuthorizationTokenRepositoryInterface
     */
    private AuthorizationTokenRepositoryInterface $repository;

    public function __construct(
        AuthorizationTokenRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, string $actionId)
    {
        /**
         * Check if authorization token exists in request
         */
        if (!$request->input('authorization_token')) {
            return $this->forbiddenRequestResponse();
        }

        /**
         * Try to decode authorization token and get database entry
         */
        try {
            $jwt = (array)JWT::decode($request->input('authorization_token'), new Key(config('oauth.public_key'), 'RS256'));
            $token = $this->repository->get($jwt['jti']);
        } catch (Exception $e) {
            return $this->forbiddenRequestResponse();
        }

        /**
         * Check that the authorization token is not revoked
         */
        if ($token->revoked) {
            return $this->forbiddenRequestResponse();
        }

        /**
         * Check that the authorization token was issued to the authenticated user
         */
        if ($token->user_id !== $request->user()->uuid) {
            return $this->forbiddenRequestResponse();
        }

        /**
         * Check that the authorization token was issued for the requested action
         */
        if ($token->action_id !== $actionId) {
            return $this->forbiddenRequestResponse();
        }

        return $next($request);
    }

    /**
     * @throws Exception
     */
    public function terminate(Request $request, Response $response)
    {
        try {
            $jwt = (array)JWT::decode($request->input('authorization_token'), new Key(config('oauth.public_key'), 'RS256'));
        } catch (ExpiredException $e) {
        }

        /**
         * Revoke authorization token if response is successful
         */
        if ($response->isSuccessful()) {
            $this->repository->update($jwt['jti'], [
                'revoked' => 1
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    protected function forbiddenRequestResponse(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'forbidden'
        ], 403);
    }
}
