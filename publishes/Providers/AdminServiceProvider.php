<?php

namespace App\Providers;

use App\Http\Terranet\Administrator\Dashboard\BlankPanel;
use App\Http\Terranet\Administrator\Dashboard\DatabasePanel;
use App\Http\Terranet\Administrator\Dashboard\MembersPanel;
use Illuminate\Support\ServiceProvider;
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
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('scaffold.dashboard', function () {
            return $this->dashboard(new Manager());
        });
    }
}
