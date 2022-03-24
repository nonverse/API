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
     * @param $uuid
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $uuid)
    {
        if ($uuid === $request->user()->uuid) {
            return response('Cannot perform this action on self', 403);
        }

        return $next($request);
    }
}
