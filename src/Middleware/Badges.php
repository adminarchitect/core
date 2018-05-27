<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Terranet\Administrator\Traits\ResolvesClasses;
use Terranet\Administrator\Traits\SortsObjectsCollection;

class Badges
{
    use ResolvesClasses, SortsObjectsCollection;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $badges = Collection::make([]);

        $this->collectBadges(function ($fileInfo) use ($badges) {
            $this->resolveClass($fileInfo, function ($badge) use ($badges) {
                $badges->push($badge);
            });
        });

        $this->registerCollection(
            $this->sortCollection($badges)
        );

        return $next($request);
    }

    protected function collectBadges(Closure $callback)
    {
        $files = app('files')->exists(
            $path = app_path(app('scaffold.config')->get('paths.badges', 'Http/Terranet/Administrator/Badges'))
        ) ? app('files')->allFiles($path) : [];

        return Collection::make($files)->each($callback);
    }

    /**
     * @param $badges
     *
     * @return mixed
     */
    protected function registerCollection($badges)
    {
        return app()->instance('scaffold.badges', $badges);
    }
}
