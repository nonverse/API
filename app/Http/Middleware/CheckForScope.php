<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckForScope
{
    /**
     * Handle an incoming request.
     * Check if the user's token has one of the required scopes
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param string $scopes
     * @return JsonResponse|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $scopes): JsonResponse|RedirectResponse|Response
    {
        foreach (explode(",", $scopes) as $scope) {
            if ($request->user()->tokenCan($scope)) {
                return $next($request);
            }
        }
        return new JsonResponse([
            'error' => 'forbidden'
        ], 403);
    }
}
