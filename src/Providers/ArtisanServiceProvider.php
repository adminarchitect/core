<?php

namespace Terranet\Administrator\Providers;

use Illuminate\Support\ServiceProvider;
use Terranet\Administrator\Console\ActionMakeCommand;
use Terranet\Administrator\Console\ActionsMakeCommand;
use Terranet\Administrator\Console\AdministratorCreateCommand;
use Terranet\Administrator\Console\BreadcrumbsMakeCommand;
use Terranet\Administrator\Console\FinderMakeCommand;
use Terranet\Administrator\Console\LanguagesMakeCommand;
use Terranet\Administrator\Console\PanelMakeCommand;
use Terranet\Administrator\Console\PublishCommand;
use Terranet\Administrator\Console\ResourceMakeCommand;
use Terranet\Administrator\Console\SaverMakeCommand;
use Terranet\Administrator\Console\SettingsMakeCommand;
use Terranet\Administrator\Console\TemplateMakeCommand;

class ArtisanServiceProvider extends ServiceProvider
{
    protected $commands = [
        'AdminPublish' => 'command.administrator.publish',
        'AdminCreate' => 'command.administrator.create',
        'AdminModule' => 'command.administrator.module',
        'AdminActions' => 'command.administrator.actions',
        'AdminAction' => 'command.administrator.action',
        'AdminSaver' => 'command.administrator.saver',
        'AdminTemplate' => 'command.administrator.template',
        'AdminPanel' => 'command.administrator.panel',
        'AdminFinder' => 'command.administrator.finder',
        'AdminSettings' => 'command.administrator.settings',
        'AdminLanguages' => 'command.administrator.languages',
        'AdminBreadcrumbs' => 'command.administrator.breadcrumbs',
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            \call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }

    protected function registerAdminPublishCommand()
    {
        $this->app->singleton('command.administrator.publish', function () {
            return new PublishCommand();
        });
    }

    protected function registerAdminCreateCommand()
    {
        $this->app->singleton('command.administrator.create', function ($app) {
            return new AdministratorCreateCommand($app['hash']);
        });
    }

    protected function registerAdminModuleCommand()
    {
        $this->app->singleton('command.administrator.module', function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    protected function registerAdminActionsCommand()
    {
        $this->app->singleton('command.administrator.actions', function ($app) {
            return new ActionsMakeCommand($app['files']);
        });
    }

    protected function registerAdminActionCommand()
    {
        $this->app->singleton('command.administrator.action', function ($app) {
            return new ActionMakeCommand($app['files']);
        });
    }

    protected function registerAdminPanelCommand()
    {
        $this->app->singleton('command.administrator.panel', function ($app) {
            return new PanelMakeCommand($app['files']);
        });
    }

    protected function registerAdminSaverCommand()
    {
        $this->app->singleton('command.administrator.saver', function ($app) {
            return new SaverMakeCommand($app['files']);
        });
    }

    protected function registerAdminTemplateCommand()
    {
        $this->app->singleton('command.administrator.template', function ($app) {
            return new TemplateMakeCommand($app['files']);
        });
    }

    protected function registerAdminFinderCommand()
    {
        $this->app->singleton('command.administrator.finder', function ($app) {
            return new FinderMakeCommand($app['files']);
        });
    }

    protected function registerAdminBreadcrumbsCommand()
    {
        $this->app->singleton('command.administrator.breadcrumbs', function ($app) {
            return new BreadcrumbsMakeCommand($app['files']);
        });
    }

    protected function registerAdminSettingsCommand()
    {
        $this->app->singleton('command.administrator.settings', function ($app) {
            return new SettingsMakeCommand($app['files']);
        });
    }

    protected function registerAdminLanguagesCommand()
    {
        $this->app->singleton('command.administrator.languages', function ($app) {
            return new LanguagesMakeCommand($app['files']);
        });
    }
}
