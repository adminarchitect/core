<?php

namespace App\Providers;

use App\Http\Terranet\Administrator\Dashboard\BlankPanel;
use App\Http\Terranet\Administrator\Dashboard\DatabasePanel;
use App\Http\Terranet\Administrator\Dashboard\MembersPanel;
use Illuminate\Support\ServiceProvider;
use Pingpong\Menus\Menu;
use Pingpong\Menus\MenuBuilder;
use Pingpong\Menus\MenuItem;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Dashboard\Manager;
use Terranet\Administrator\Dashboard\Row;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Dashboard panels registration.
     *
     * @param Manager $dashboard
     * @return Manager
     */
    protected function dashboard(Manager $dashboard)
    {
        return $dashboard
            ->row(function (Row $row) {
                $row->panel(new BlankPanel)->setWidth(12);
            })
            ->row(function (Row $row) {
                $row->panel(new MembersPanel)->setWidth(6);
                $row->panel(new DatabasePanel)->setWidth(6);
            });
    }

    /**
     * @param Menu $navigation
     */
    protected function navigation(Menu $navigation)
    {
        $navigation->create(Navigable::MENU_SIDEBAR, function (MenuBuilder $sidebar) {
            // Dashboard
            $sidebar->route('scaffold.dashboard', trans('administrator::module.dashboard'), [], 1, [
                'id' => 'dashboard',
                'icon' => 'fa fa-home',
                'active' => str_is(request()->route()->getName(), 'scaffold.dashboard'),
            ]);

            // Create "resources" group
            $sidebar->dropdown(trans('administrator::module.groups.resources'), function (MenuItem $sub) {
            }, 2, ['id' => 'groups', 'icon' => 'fa fa-qrcode']);
        });

        $navigation->create(Navigable::MENU_TOOLS, function (MenuBuilder $tools) {
            if (app('scaffold.config')->get('file_manager')) {
                $tools->url(
                    route('scaffold.media'),
                    trans('administrator::buttons.media'),
                    98,
                    ['icon' => 'fa fa-file-text-o']
                );
            }

            if (app('scaffold.config')->get('translations.enabled')) {
                $tools->url(
                    route('scaffold.translations.index'),
                    trans('administrator::buttons.translations'),
                    99,
                    ['icon' => 'fa fa-globe']
                );
            }

            $tools->url(
                route('scaffold.logout'),
                trans('administrator::buttons.logout'),
                100,
                ['icon' => 'fa fa-mail-forward']
            );
        });

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
}
