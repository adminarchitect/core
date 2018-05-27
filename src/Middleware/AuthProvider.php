<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AuthProvider.
 */
class AuthProvider
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        auth()->shouldUse('admin');

        return $next($request);
    }
}
