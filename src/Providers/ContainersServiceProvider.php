<?php

namespace Terranet\Administrator\Providers;

use DaveJamesMiller\Breadcrumbs\BreadcrumbsManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Module\Filtrable;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Dashboard\Manager;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Filter;
use Terranet\Administrator\Schema;
use Terranet\Administrator\Services\Sorter;
use Terranet\Administrator\Services\Template;
use Terranet\Localizer\Locale;

class ContainersServiceProvider extends ServiceProvider
{
    protected $containers = [
        'AdminConfig' => 'scaffold.config',
        'AdminResource' => 'scaffold.module',
        'AdminModel' => 'scaffold.model',
        'AdminWidgets' => 'scaffold.widgets',
        'AdminSchema' => 'scaffold.schema',
        'AdminSortable' => 'scaffold.sortable',
        'AdminTemplate' => 'scaffold.template',
        'AdminBreadcrumbs' => 'scaffold.breadcrumbs',
        'AdminTranslations' => 'scaffold.translations',
        'AdminAnnotations' => 'scaffold.annotations',
    ];

    public function register()
    {
        foreach (array_keys($this->containers) as $container) {
            $method = "register{$container}";

            \call_user_func_array([$this, $method], []);
        }
//
//        $this->app->bind(Module::class, function ($app) {
//            return $app['scaffold.module'];
//        });
    }

    protected function registerAdminAnnotations()
    {
        $this->app->singleton('scaffold.annotations', function () {
            AnnotationRegistry::registerUniqueLoader('class_exists');

            $reader = new SimpleAnnotationReader();
            $reader->addNamespace('\\Terranet\\Administrator\\Annotations');

            return $reader;
        });
    }

    protected function registerAdminConfig()
    {
        $this->app->singleton('scaffold.config', function ($app) {
            $config = $app['config']['administrator'];

            return new Config((array) $config);
        });
    }

    protected function registerAdminTranslations()
    {
        // Draft: Mui configuration
        // Goal: sometimes there is a case when few content managers (admins) override the same translatable content (files, db, etc...)
        // This service allows to make some locales readonly:
        //  1. they are available in UI in order to preserve the context
        //  2. they are protected from saving process
        // Making locale(s) Readonly remains for Dev's side: the recommended way - use a custom Middleware.
        // ex.: app('scaffold.translations')->setReadonly([1, 2, 3])
        $this->app->singleton('scaffold.translations', function ($app) {
            $service = new class() {
                protected $readonly = [];

                public function __construct()
                {
                    $this->setReadonly(config('administrator.translations.readonly', []));
                }

                /**
                 * Set ReadOnly locales.
                 *
                 * @param array $readonly
                 *
                 * @return self
                 */
                public function setReadonly(array $readonly = []): self
                {
                    $this->readonly = (array) $readonly;

                    return $this;
                }

                /**
                 * Check if a Locale is ReadOnly.
                 *
                 * @param $locale
                 *
                 * @return bool
                 */
                public function readonly($locale)
                {
                    if ($locale instanceof Locale) {
                        $locale = $locale->id();
                    }

                    return \in_array((int) $locale, $this->readonly, true);
                }
            };

            return $service;
        });
    }

    protected function registerAdminResource()
    {
        $this->app->singleton('scaffold.module', function ($app) {
            if (\in_array(
                $app['router']->currentRouteName(),
                ['scaffold.settings.edit', 'scaffold.settings.update'],
                true
            )) {
                return $app['scaffold.module.settings'];
            }

            if (($router = $app['router']->current()) &&
                ($module = $router->parameter('module')) &&
                Arr::has($app, $key = "scaffold.module.{$module}")
            ) {
                return Arr::get($app, $key);
            }
        });
    }

    protected function registerAdminModel()
    {
        $this->app->singleton('scaffold.model', function ($app) {
            if (($id = $app['router']->current()->parameter('id'))
                && ($finder = app('scaffold.module')->finderInstance())) {
                return $finder->find($id);
            }
        });
    }

    protected function registerAdminWidgets()
    {
        $this->app->singleton('scaffold.widgets', function () {
            if (($module = app('scaffold.module'))) {
                return $module->widgets(new Manager());
            }

            return new Manager();
        });

        $this->app->singleton('scaffold.cards', function () {
            if (($module = app('scaffold.module'))) {
                return $module->cards(new Manager());
            }

            return new Manager();
        });
    }

    protected function registerAdminSchema()
    {
        $this->app->singleton('scaffold.schema', function ($app) {
            if ($schema = $app['db']->connection()->getDoctrineSchemaManager()) {
                // fix dbal missing types
                $platform = $schema->getDatabasePlatform();
                $platform->registerDoctrineTypeMapping('enum', 'string');
                $platform->registerDoctrineTypeMapping('set', 'string');

                return new Schema($schema);
            }
        });
    }

    protected function registerAdminSortable()
    {
        $this->app->singleton('scaffold.sortable', function ($app) {
            if ($module = $app['scaffold.module']) {
                return new Sorter(
                    $module instanceof Sortable ? $module->sortable() : [],
                    method_exists($module, 'sortDirection') ? $module->sortDirection() : 'desc'
                );
            }
        });
    }

    protected function registerAdminTemplate()
    {
        $this->app->singleton('scaffold.template', function ($app) {
            // check for resource template
            $handler = ($module = $app['scaffold.module']) ? $module->template() : Template::class;
            $handler = new $handler();

            if (!$handler instanceof TemplateProvider) {
                throw new Exception('Templates handler must implement '.TemplateProvider::class.' contract');
            }

            return $handler;
        });
    }

    protected function registerAdminBreadcrumbs()
    {
        $this->app->singleton('scaffold.breadcrumbs', function ($app) {
            if (!class_exists(BreadcrumbsManager::class)) {
                throw new Exception('Please install `davejamesmiller/laravel-breadcrumbs:^5.2` package.');
            }

            if ($module = $app['scaffold.module']) {
                $provider = $module->breadcrumbs();

                return new $provider($app->make(BreadcrumbsManager::class), $app->make('scaffold.module'));
            }
        });
    }
}
