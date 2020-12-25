<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pingpong\Menus\Menu;
use Pingpong\Menus\MenuBuilder;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Traits\ResolvesClasses;

class Resources
{
    use ResolvesClasses;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

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
        $this->collectModules(function ($fileInfo) use ($request) {
            $this->resolveClass($fileInfo, function ($module) use ($request) {
                if ($module instanceof Module) {
                    $this->registerModule($module);
                }

                if ($this->navigableResource($module, $request)
                    && ($navigation = $this->application->make('scaffold.navigation'))
                ) {
                    $this->makeNavigation($module, $navigation);
                }
            });
        });

        return $next($request);
    }

    protected function collectModules(Closure $callback)
    {
        $resources = Collection::make($this->application['files']->allFiles(
            app_path($this->application['scaffold.config']->get('paths.module'))
        ))->each($callback);

        $this->application->instance('scaffold.resources', $resources);

        return $resources;
    }

    /**
     * @param $module
     *
     * @return $this
     */
    protected function registerModule(Module $module)
    {
        $modules = $this->application['scaffold.modules'] ?? collect();

        if (!$modules->contains($module)) {
            $modules->push($module);
        }
        $this->application->instance('scaffold.modules', $modules);

        $this->application->instance("scaffold.module.{$module->url()}", $module);

        return $this;
    }

    /**
     * Make sure the resource is Navigable && Should be visible.
     *
     * @param $module
     * @param Request $request
     *
     * @return bool
     */
    protected function navigableResource($module, Request $request)
    {
        return $module instanceof Navigable && $module->showIf($request);
    }

    /**
     * @param Module $module
     * @param $navigation
     */
    protected function makeNavigation(Module $module, $navigation)
    {
        // Force ordering menus
        $navigation = $navigation->instance(
            $this->validateContainer($module, $navigation)
        );

        $order = $module->order() ?: 1;

        if ($group = $module->group()) {
            $navigation = $this->findOrCreateGroup($module, $navigation, $group);
            $order = $module->order() ?: \count($navigation->getChilds()) + 1;
        }

        if (Navigable::AS_LINK === $module->showAs()) {
            $navigation->route(
                'scaffold.index',
                $module->title(),
                array_merge(['module' => $module->url()], $module->navigableParams()),
                $order,
                array_merge(
                    $module->linkAttributes(),
                    [
                        'active' => function () use ($module) {
                            return $this->isActive($module);
                        },
                    ]
                )
            );
        } else {
            $navigation->url(
                '#',
                $module->title(),
                $order,
                array_merge(
                    $module->linkAttributes(),
                    [
                        'active' => function () use ($module) {
                            return $module->showIf();
                        },
                    ]
                )
            );
        }
    }

    /**
     * @param $module
     * @param $navigation
     *
     * @return mixed
     */
    protected function validateContainer(Module $module, Menu $navigation)
    {
        $container = $module->navigableIn();

        if (!array_key_exists($container, $navigation->all())) {
            $message =
                "Can not add \"{$module->title()}\" to \"{$container}\" menu. Available menus: ".
                implode(', ', array_keys($navigation->all())).'.';

            throw new \InvalidArgumentException($message);
        }

        return $container;
    }

    /**
     * @param $module
     * @param $navigation
     * @param $group
     *
     * @return mixed
     */
    protected function findOrCreateGroup(Module $module, MenuBuilder $navigation, $group)
    {
        if (!$sub = $navigation->whereTitle($group)) {
            $sub = $navigation->dropdown($group, function () {
            }, 99, ['id' => $module->url(), 'icon' => 'fa fa-folder']);
        }

        return $sub;
    }

    protected function isActive($module)
    {
        static $checked = [];
        $module = $module->url();

        if (!array_key_exists($module, $checked)) {
            $urls = array_map(function ($url) {
                return trim($url, '/');
            }, [
                'current' => \URL::getRequest()->getPathInfo(),
                'create' => route('scaffold.create', ['module' => $module], false),
                'module' => config('administrator.prefix')."/{$module}",
            ]);

            $checked[$module] = Str::startsWith($urls['current'], $urls['module']) && ($urls['current'] !== $urls['create']);
        }

        return $checked[$module];
    }
}
