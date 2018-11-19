<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizePaths
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
        if (!empty($paths = $this->guardedPaths($request))) {
            $request->merge(
                array_map([$this, 'sanitizePath'], $paths)
            );
        }

        return $next($request);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function sanitizePath($path)
    {
        if (\is_array($path)) {
            return array_map([$this, 'sanitizePath'], $path);
        }

        $path = iconv($encoding = 'UTF-8', "$encoding//IGNORE//TRANSLIT", $path);

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

        return implode(\DIRECTORY_SEPARATOR, $safe);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function guardedPaths(Request $request)
    {
        return array_only(
            $request->all(),
            ['path', 'basedir', 'directories', 'from', 'to', 'name']
        );
    }
}
