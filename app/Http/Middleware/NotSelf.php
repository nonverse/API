<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotSelf
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->route('uuid') === $request->user()->uuid) {
            return response('Cannot perform this action on self', 403);
        }

        return $next($request);
    }
}
