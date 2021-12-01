<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;

class HasValidProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Profile::query()->where('uuid', $request->user()->uuid)->exists()) {
            return response('Profile not found', 404);
        }
        return $next($request);
    }
}
