<?php

namespace Terranet\Administrator\Dashboard\Panels;

use Terranet\Administrator\Dashboard\Panel;
use Terranet\Administrator\Traits\Stringify;
use function admin\db\connection;

class DatabasePanel extends Panel
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
        if (connection('mysql')) {
            return $this->connection()->select($this->connection()->raw('SHOW TABLE STATUS'));
        }

        return collect([]);
    }

    protected function connection()
    {
        return app('db');
    }
}
