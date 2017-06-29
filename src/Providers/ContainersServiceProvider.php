<?php

namespace Terranet\Administrator\Providers;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use Terranet\Administrator\ActionsManager;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Module\Filtrable;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Contracts\Services\CrudActions;
use Terranet\Administrator\Contracts\Services\Finder;
use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Filter;
use Terranet\Administrator\Schema;
use Terranet\Administrator\Services\MagnetParams;
use Terranet\Administrator\Services\Sorter;
use Terranet\Administrator\Services\Template;
use Terranet\Administrator\Services\Widgets;

class ContainersServiceProvider extends ServiceProvider
{
    protected $containers = [
        'AdminConfig' => 'scaffold.config',
        'AdminResource' => 'scaffold.module',
        'AdminModel' => 'scaffold.model',
        'AdminWidget' => 'scaffold.widget',
        'AdminSchema' => 'scaffold.schema',
        'AdminSortable' => 'scaffold.sortable',
        'AdminFilter' => 'scaffold.filter',
        'AdminColumns' => 'scaffold.columns',
        'AdminActions' => 'scaffold.actions',
        'AdminTemplate' => 'scaffold.template',
        'AdminForm' => 'scaffold.form',
        'AdminMagnet' => 'scaffold.magnet',
        'AdminFinder' => 'scaffold.finder',
        'AdminBreadcrumbs' => 'scaffold.breadcrumbs',
        'AdminNavigation' => 'scaffold.navigation',
        'AdminDashboard' => 'scaffold.dashboard',
    ];

    public function register()
    {
        foreach (array_keys($this->containers) as $container) {
            $method = "register{$container}";

            call_user_func_array([$this, $method], []);
        }

        $this->app->bind(Module::class, function ($app) {
            return $app['scaffold.module'];
        });
    }

    protected function registerAdminConfig()
    {
        $this->app->singleton('scaffold.config', function ($app) {
            $config = $app['config']['administrator'];

            return new Config((array) $config);
        });
    }

    protected function registerAdminDashboard()
    {
        $this->app->singleton('scaffold.dashboard', function () {
            if ($factory = config('administrator.dashboard')) {
                return app($factory)->make();
            }
        });
    }

    protected function registerAdminResource()
    {
        $this->app->singleton('scaffold.module', function ($app) {
            if (in_array($app['router']->currentRouteName(), ['scaffold.settings.edit', 'scaffold.settings.update'])) {
                return $app['scaffold.module.settings'];
            }

            if (($router = $app['router']->current()) &&
                ($module = $router->parameter('module')) &&
                array_has($app, $key = "scaffold.module.{$module}")
            ) {
                return array_get($app, $key);
            }
        });
    }

    protected function registerAdminModel()
    {
        $this->app->singleton('scaffold.model', function ($app) {
            if (($finder = app('scaffold.finder'))
                && ($id = $app['router']->current()->parameter('id'))
            ) {
                return $finder->find($id);
            }
        });
    }

    protected function registerAdminWidget()
    {
        $this->app->singleton('scaffold.widget', function ($app) {
            if ($module = $app['scaffold.module']) {
                return new Widgets($module->widgets());
            }
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

    protected function registerAdminColumns()
    {
        $this->app->singleton('scaffold.columns', function ($app) {
            if ($module = $app['scaffold.module']) {
                return $module->columns();
            }
        });
    }

    protected function registerAdminActions()
    {
        $this->app->singleton('scaffold.actions', function ($app) {
            if ($module = $app['scaffold.module']) {
                $handler = $module->actions();
                $handler = new $handler($module);

                if (!$handler instanceof CrudActions) {
                    throw new Exception('Actions handler must implement ' . CrudActions::class . ' contract');
                }

                return new ActionsManager($handler, $module);
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
                throw new Exception('Templates handler must implement ' . TemplateProvider::class . ' contract');
            }

            return $handler;
        });
    }

    protected function registerAdminForm()
    {
        $this->app->singleton('scaffold.form', function ($app) {
            if ($module = $app['scaffold.module']) {
                return $module->form();
            }
        });
    }

    protected function registerAdminMagnet()
    {
        $this->app->singleton('scaffold.magnet', function ($app) {
            if ($module = $app['scaffold.module']) {
                return new MagnetParams($app['request'], $module->magnetParams());
            }
        });
    }

    protected function registerAdminFilter()
    {
        $this->app->singleton('scaffold.filter', function ($app) {
            if ($module = $app['scaffold.module']) {
                $filters = $module instanceof Filtrable ? $module->filters() : null;
                $scopes = $module instanceof Filtrable ? $module->scopes() : null;

                return new Filter($app['request'], $filters, $scopes);
            }
        });
    }

    protected function registerAdminFinder()
    {
        $this->app->singleton('scaffold.finder', function ($app) {
            if ($module = $app['scaffold.module']) {
                $finder = $module->finder();
                $finder = new $finder($module);

                if (!$finder instanceof Finder) {
                    throw new Exception('Items Finder must implement ' . Finder::class . ' contract');
                }

                return $finder;
            }
        });
    }

    protected function registerAdminBreadcrumbs()
    {
        $this->app->singleton('scaffold.breadcrumbs', function ($app) {
            if ($module = $app['scaffold.module']) {
                $provider = $module->breadcrumbs();

                return new $provider($app->make('breadcrumbs'), $app->make('scaffold.module'));
            }
        });
    }

    protected function registerAdminNavigation()
    {
        $this->app->singleton('scaffold.navigation', function ($app) {
            if ($factory = $app['scaffold.config']->get('menu')) {
                return app($factory)->make();
            }
        });
    }
}
