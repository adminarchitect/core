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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        abort_unless(config('administrator.file_manager'), 404, 'Route not found.');

        return $next($request);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function sanitizePath($path)
    {
        if (is_array($path)) {
            return array_map([$this, 'sanitizePath'], $path);
        }

        $path = iconv($encoding = "UTF-8", "$encoding//IGNORE//TRANSLIT", $path);

        $parts = explode('/', $path);
        $safe = [];
        foreach ($parts as $part) {
            if (empty($part) || ('.' === $part)) {
                continue;
            }

            if ('..' === $part) {
                array_pop($safe);
                continue;
            }

            $safe[] = $part;
        }

        return implode(DIRECTORY_SEPARATOR, $safe);
    }
}
