<?php

namespace Terranet\Administrator\Dashboard;

use Terranet\Administrator\Dashboard\Manager as DashboardManager;

abstract class Factory
{
    /**
     * @var DashboardManager
     */
    protected $dashboard;

    public function __construct(DashboardManager $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function make()
    {
        return $this->registerPanels();
    }

    /**
     * Register dashboard panels.
     *
     * @return Manager
     */
    abstract protected function registerPanels();
}
