<?php

namespace Terranet\Administrator\Dashboard;

use App\Http\Terranet\Administrator\Dashboard\BlankPanel;
use App\Http\Terranet\Administrator\Dashboard\DatabasePanel;
use App\Http\Terranet\Administrator\Dashboard\GoogleAnalyticsPanel;
use App\Http\Terranet\Administrator\Dashboard\MembersPanel;
use Terranet\Administrator\Dashboard\Manager as DashboardManager;
use Terranet\Administrator\Dashboard\Manager;

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