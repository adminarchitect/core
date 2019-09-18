<?php

namespace App\Providers;

use App\Http\Terranet\Administrator\Dashboard\BlankPanel;
use App\Http\Terranet\Administrator\Dashboard\DatabasePanel;
use App\Http\Terranet\Administrator\Dashboard\MembersPanel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Pingpong\Menus\Menu;
use Pingpong\Menus\MenuBuilder;
use Pingpong\Menus\MenuItem;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Dashboard\Manager;
use Terranet\Administrator\Dashboard\Row;
use Terranet\Options\Manager as OptionsManager;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Dashboard panels registration.
     *
     * @param  Manager  $dashboard
     * @return Manager
     */
    protected function dashboard(Manager $dashboard)
    {
        return $dashboard
            ->row(static function (Row $row) {
                $row->panel(new BlankPanel())->setWidth(12);
            })
            ->row(static function (Row $row) {
                $row->panel(new MembersPanel())->setWidth(6);
                $row->panel(new DatabasePanel())->setWidth(6);
            });
    }

    /**
     * @param  Menu  $navigation
     * @return Menu
     */
    protected function navigation(Menu $navigation)
    {
        $this->initSidebar($navigation)
            ->initToolbar($navigation);

        return $navigation;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('scaffold.dashboard', function () {
            return $this->dashboard(new Manager());
        });

        $this->app->singleton('scaffold.navigation', function ($app) {
            return $this->navigation(new Menu($app['view'], $app['config']));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes();
    }

    /**
     * Register AdminArchitect routes.
     */
    protected function routes()
    {
        Architect::routes()
            ->withAuthenticationRoutes()
            ->withTranslationRoutes()
            ->withMediaRoutes()
            ->withSettingRoutes()
            ->withExtraRoutes(function () {
                $path = base_path('routes/admin.php');

                if (file_exists($path)) {
                    $this->loadRoutesFrom($path);
                }
            });
    }

    /**
     * @param  Menu  $navigation
     * @return AdminServiceProvider
     */
    protected function initSidebar(Menu $navigation): self
    {
        $navigation->create(Navigable::MENU_SIDEBAR, function (MenuBuilder $sidebar) {
            $this->withDashboard($sidebar);

            // Create "resources" group
            $sidebar->dropdown(trans('administrator::module.groups.resources'), static function (MenuItem $sub) {
                // $sub->route();
            }, 2, ['id' => 'groups', 'icon' => 'fa fa-qrcode']);
        });

        return $this;
    }

    /**
     * @param  Menu  $navigation
     * @return AdminServiceProvider
     */
    protected function initToolbar(Menu $navigation): self
    {
        $navigation->create(Navigable::MENU_TOOLS, function (MenuBuilder $tools) {
            $this->withMedia($tools)
                ->withSettings($tools)
                ->withTranslations($tools);

            $tools->url(
                route('scaffold.logout'),
                trans('administrator::buttons.logout'),
                100,
                ['icon' => 'fa fa-mail-forward']
            );
        });

        return $this;
    }

    /**
     * @param  MenuBuilder  $sidebar
     * @return $this
     */
    public function withDashboard(MenuBuilder $sidebar): self
    {
        $sidebar->route('scaffold.dashboard', trans('administrator::module.dashboard'), [], 1, [
            'id' => 'dashboard',
            'icon' => 'fa fa-home',
            'active' => Str::is(request()->route()->getName(), 'scaffold.dashboard'),
        ]);

        return $this;
    }

    /**
     * @param  MenuBuilder  $tools
     * @return $this
     */
    protected function withMedia(MenuBuilder $tools): self
    {
        if (config('administrator.file_manager.enabled')) {
            $tools->url(
                route('scaffold.media'),
                trans('administrator::buttons.media'),
                1,
                ['icon' => 'fa fa-file-text-o']
            );
        }

        return $this;
    }

    /**
     * @param  MenuBuilder  $tools
     * @return $this
     */
    protected function withSettings(MenuBuilder $tools): self
    {
        if (config('administrator.settings.enabled') && class_exists(OptionsManager::class, true)) {
            $tools->url(
                route('scaffold.settings.edit'),
                trans('administrator::module.resources.settings'),
                2,
                ['icon' => 'fa fa-gears']
            );
        }

        return $this;
    }

    /**
     * @param  MenuBuilder  $tools
     * @return $this
     */
    protected function withTranslations(MenuBuilder $tools): self
    {
        if (config('administrator.translations.enabled')) {
            $tools->url(
                route('scaffold.translations.index'),
                trans('administrator::buttons.translations'),
                3,
                ['icon' => 'fa fa-globe']
            );
        }

        return $this;
    }
}
