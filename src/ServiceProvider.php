<?php

namespace Terranet\Administrator;

use App\User;
use Codesleeve\LaravelStapler\Providers\L5ServiceProvider as StaplerServiceProvider;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Creativeorange\Gravatar\Facades\Gravatar;
use Creativeorange\Gravatar\GravatarServiceProvider;
use DaveJamesMiller\Breadcrumbs\Facade as BreadcrumbsFacade;
use DaveJamesMiller\Breadcrumbs\ServiceProvider as BreadcrumbsServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Pingpong\Menus\MenuFacade;
use Pingpong\Menus\MenusServiceProvider;
use Terranet\Administrator\Middleware\Web;
use Terranet\Administrator\Providers\ArtisanServiceProvider;
use Terranet\Administrator\Providers\ContainersServiceProvider;
use Terranet\Administrator\Providers\EventServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $baseDir = realpath(dirname(__FILE__) . '/../');

        /*
         * Publish & Load routes
         */
        $packageRoutes = "{$baseDir}/publishes/routes.php";
        $publishedRoutes = app_path('Http/Terranet/Administrator/routes.php');
        $this->publishes([$packageRoutes => $publishedRoutes], 'routes');

        if (!$this->app->routesAreCached()) {
            $routesFile = file_exists($publishedRoutes) ? $publishedRoutes : $packageRoutes;

            /** @noinspection PhpIncludeInspection */
            require $routesFile;
        }

        /*
         * Publish & Load configuration
         */
        $this->publishes(["{$baseDir}/publishes/config.php" => config_path('administrator.php')], 'config');
        $this->mergeConfigFrom("{$baseDir}/publishes/config.php", 'administrator');

        /*
         * Publish & Load views, assets
         */
        $this->publishes(["{$baseDir}/publishes/resources" => resource_path('assets/administrator')], 'assets');
        $this->publishes(["{$baseDir}/publishes/views" => base_path('resources/views/vendor/administrator')], 'views');
        $this->loadViewsFrom("{$baseDir}/publishes/views", 'administrator');

        /*
         * Publish & Load translations
         */
        $this->publishes(
            ["{$baseDir}/publishes/translations" => base_path('resources/lang/vendor/administrator')],
            'translations'
        );
        $this->loadTranslationsFrom("{$baseDir}/publishes/translations", 'administrator');

        /*
         * Publish default Administrator Starter Kit: modules, dashboard panels, policies, etc...
         */
        $this->publishes(
            ["{$baseDir}/publishes/Modules" => app_path('Http/Terranet/Administrator/Modules')],
            'boilerplate'
        );
        $this->publishes(
            ["{$baseDir}/publishes/Dashboard" => app_path('Http/Terranet/Administrator/Dashboard')],
            'boilerplate'
        );
        $this->publishes(
            ["{$baseDir}/publishes/Navigation.php" => app_path('Http/Terranet/Administrator/Navigation.php')],
            'navigation'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        if (guarded_auth()) {
            $this->configureAuth();
        } else {
            $this->fakeWebMiddleware();
        }

        $dependencies = [
            ArtisanServiceProvider::class,
            ContainersServiceProvider::class,
            EventServiceProvider::class,
            BreadcrumbsServiceProvider::class => [
                'Breadcrumbs' => BreadcrumbsFacade::class,
            ],
            HtmlServiceProvider::class => [
                'Html' => HtmlFacade::class,
                'Form' => FormFacade::class,
            ],
            StaplerServiceProvider::class,
            MenusServiceProvider::class => [
                'AdminNav' => MenuFacade::class,
            ],
            GravatarServiceProvider::class => [
                'Gravatar' => Gravatar::class,
            ],
        ];

        array_walk($dependencies, function ($package, $provider) {
            if (is_string($package) && is_numeric($provider)) {
                $provider = $package;
                $package = null;
            }

            if (!$this->app->getProvider($provider)) {
                $this->app->register($provider);

                if (is_array($package)) {
                    foreach ($package as $alias => $facade) {
                        if (class_exists($alias)) {
                            continue;
                        }

                        class_alias($facade, $alias);
                    }
                }
            }
        });
    }

    protected function configureAuth()
    {
        if (!Config::has('auth.guards.admin')) {
            Config::set('auth.guards.admin', [
                'driver' => 'session',
                'provider' => 'admins',
            ]);
        }

        if (!Config::has('auth.providers.admins')) {
            Config::set('auth.providers.admins', [
                'driver' => 'eloquent',
                'model' => config('administrator.auth.model', User::class),
            ]);
        }
    }

    /**
     * Laravel 5.1 does not come with 'web' middlware group
     * so for back compatibility with Laravel 5.1 & Laravel 5.2
     * we add this faked Middleware
     */
    protected function fakeWebMiddleware()
    {
        if (!app('Illuminate\Contracts\Http\Kernel')->hasMiddleware('web')) {
            app('router')->middleware('web', Web::class);
        }
    }
}
