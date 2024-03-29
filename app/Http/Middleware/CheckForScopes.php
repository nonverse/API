<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckForScopes
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param string $scopes
     * @return JsonResponse|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $scopes): JsonResponse|RedirectResponse|Response
    {
        foreach (explode(",", $scopes) as $scope) {
            if (!$request->user()->tokenCan($scope)) {
                return new JsonResponse([
                    'error' => 'forbidden'
                ], 403);
            }
        }
        return $next($request);
    }
}
