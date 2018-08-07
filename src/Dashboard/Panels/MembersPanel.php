<?php

namespace Terranet\Administrator\Dashboard\Panels;

use DB;
use Terranet\Administrator\Dashboard\DashboardPanel;
use Terranet\Administrator\Traits\Stringify;

class MembersPanel extends DashboardPanel
{
    use Stringify;

    public function render()
    {
        $weekAgo = \Carbon\Carbon::now()->subWeek();
        $monthAgo = \Carbon\Carbon::now()->subMonth();

        $total = $this->createModel()->count();
        $signedLastWeek = $this->createModel()
                                 ->where('created_at', '>=', $weekAgo)->count();
        $signedLastMonth = $this->createModel()
                                 ->where('created_at', '>=', $monthAgo)->count();
        $signedStatistics = $this->createModel()
                                 ->where('created_at', '>=', $monthAgo)
                                 ->select([DB::raw('COUNT(id) AS cnt'), DB::raw('DATE(created_at) as dt')])
                                 ->groupBy('dt')->pluck('cnt', 'dt');

        return view(app('scaffold.template')->dashboard('members'), [
            'total' => $total,
            'signed' => [
                'lastWeek' => $signedLastWeek,
                'lastMonth' => $signedLastMonth,
            ],
            'signedStatistics' => $signedStatistics,
        ]);
    }

    /**
     * @return User
     */
    protected function createModel()
    {
        $model = config('administrator.auth.model', '\App\User');

        return new $model();
    }
}
