<?php

namespace Terranet\Administrator\Dashboard\Panels;

use function admin\db\connection;
use Terranet\Administrator\Dashboard\DashboardPanel;
use Terranet\Administrator\Traits\Stringify;

class DatabasePanel extends DashboardPanel
{
    use Stringify;

    public function render()
    {
        $dbStats = $this->getDatabaseStats();

        return view(app('scaffold.template')->dashboard('database'), [
            'dbStats' => $dbStats,
        ]);
    }

    /**
     * @return mixed
     */
    protected function getDatabaseStats()
    {
        if (connection('mysql'))
            return $this->connection()->select($this->connection()->raw("SHOW TABLE STATUS"));
        return collect([]);
    }

    protected function connection()
    {
        return app('db');
    }
}
