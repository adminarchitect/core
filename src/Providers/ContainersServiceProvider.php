<?php

namespace Terranet\Administrator\Providers;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Schema;
use Terranet\Localizer\Locale;

class ContainersServiceProvider extends ServiceProvider
{
    protected $containers = [
        'AdminConfig' => 'scaffold.config',
        'AdminResource' => 'scaffold.module',
        'AdminModel' => 'scaffold.model',
        'AdminSchema' => 'scaffold.schema',
        'AdminTranslations' => 'scaffold.translations',
        'AdminAnnotations' => 'scaffold.annotations',
    ];

    public function register()
    {
        foreach (array_keys($this->containers) as $container) {
            $method = "register{$container}";

            \call_user_func_array([$this, $method], []);
        }
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
            return new class() {
                protected $readonly = [];

                public function __construct()
                {
                    $this->setReadonly(config('administrator.translations.readonly', []));
                }

                /**
                 * Set ReadOnly locales.
                 *
                 * @param array $readonly
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
        });
    }

    protected function registerAdminResource()
    {
        $this->app->singleton('scaffold.module', function (Application $app) {
            /** @var Router $router */
            $router = $app['router']->current();

            if (in_array($router->getName(), ['scaffold.settings.edit', 'scaffold.settings.update'], true)) {
                return Architect::resourceForKey('settings');
            }

            if ($key = $router->parameter('module')) {
                return Architect::resourceForKey($key);
            }
        });
    }

    protected function registerAdminModel()
    {
        $this->app->singleton('scaffold.model', function (Application $app) {
            $id = (int) $app['router']->current()->parameter('id');

            if ($id && ($finder = app('scaffold.module')->finder())) {
                return $finder->find($id);
            }
        });
    }

    protected function registerAdminSchema()
    {
        $this->app->singleton('scaffold.schema', function (Application $app) {
            /** @var AbstractSchemaManager $schema */
            if ($schema = $app['db']->connection()->getDoctrineSchemaManager()) {
                // fix dbal missing types
                $platform = $schema->getDatabasePlatform();
                $platform->registerDoctrineTypeMapping('enum', 'string');
                $platform->registerDoctrineTypeMapping('set', 'string');

                return new Schema($schema);
            }
        });
    }
}
