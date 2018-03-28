<?php

namespace Terranet\Administrator;

use Pingpong\Menus\MenuFacade;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Pingpong\Menus\MenusServiceProvider;
use Creativeorange\Gravatar\Facades\Gravatar;
use Creativeorange\Gravatar\GravatarServiceProvider;
use Czim\Paperclip\Providers\PaperclipServiceProvider;
use Terranet\Administrator\Providers\EventServiceProvider;
use DaveJamesMiller\Breadcrumbs\Facade as BreadcrumbsFacade;
use Terranet\Administrator\Providers\ArtisanServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Terranet\Administrator\Providers\ContainersServiceProvider;
use DaveJamesMiller\Breadcrumbs\ServiceProvider as BreadcrumbsServiceProvider;

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

        $this->configureAuth();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
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
            PaperclipServiceProvider::class,
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
        if (!config()->has('auth.guards.admin')) {
            config()->set('auth.guards.admin', [
                'driver' => 'session',
                'provider' => 'admins',
            ]);
        }

        if (!config()->has('auth.providers.admins')) {
            config()->set('auth.providers.admins', [
                'driver' => 'eloquent',
                'model' => config('administrator.auth.model'),
            ]);
        }
    }
}
