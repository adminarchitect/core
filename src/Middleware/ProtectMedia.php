<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProtectMedia
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        abort_unless(config('administrator.file_manager'), 404, 'Route not found.');

        return $next($request);
    }
}
