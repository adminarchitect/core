<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Terranet\Administrator\AuthUserProvider;

/**
 * Class AuthProvider
 *
 * @package Terranet\Administrator\Middleware
 */
class AuthProvider
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app('auth.driver')->setProvider(
            new AuthUserProvider(app('hash'), app('scaffold.config')->get('auth.model'))
        );

        return $next($request);
    }
}