<?php

namespace Terranet\Administrator\Dashboard\Panels;

use Carbon\Carbon;
use Spatie\Analytics\AnalyticsServiceProvider;
use Spatie\Analytics\Period;
use Terranet\Administrator\Dashboard\DashboardPanel;
use Terranet\Administrator\Traits\Stringify;

class GoogleAnalyticsPanel extends DashboardPanel
{
    use Stringify;

    /**
     * Widget contents.
     *
     * @return mixed string|View
     */
    public function render()
    {
        $period = $this->period();

        $dailyStats = $this->dependencyInstalled() && config('analytics.view_id')
            ? Analytics::fetchTotalVisitorsAndPageViews($period)
            : $dailyStats = $this->fakeData($period);

        $visitors = $dailyStats->sum('visitors');
        $pageViews = $dailyStats->sum('pageViews');
        $maxVisitors = $dailyStats->max('visitors');

        $labels = $this->dateLabels($dailyStats);

        return view(app('scaffold.template')->dashboard('google_analytics'))->with(compact(
            'dailyStats',
            'labels',
            'visitors',
            'pageViews',
            'maxVisitors',
            'period'
        ));
    }

    /**
     * @return string
     */
    protected function abortMessage()
    {
        return
            <<<'OUT'
<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">Google Analytics.</h4>
    </div>
    <div class="panel-body">
        <p>
            Spatie Google Analytics module missing, install it by running:        
            <code>composer require spatie/laravel-analytics</code>.
            <br /><br />
            Then follow the <a href="https://github.com/spatie/laravel-analytics" target="_blank">Setup Instructions</a>.
        </p>
    </div>
</div>
OUT;
    }

    /**
     * @return bool
     */
    protected function dependencyInstalled()
    {
        return array_has(
            app()->getLoadedProviders(),
            AnalyticsServiceProvider::class
        );
    }

    /**
     * @param $dailyStats
     *
     * @return mixed
     */
    protected function dateLabels($dailyStats)
    {
        return $dailyStats->pluck('date')->map(function (Carbon $carbon) {
            return $carbon->formatLocalized('%a, %e %B %Y');
        })->toArray();
    }

    /**
     * @return Period
     */
    protected function period()
    {
        $end = Carbon::today();
        $start = Carbon::parse($end)->subMonthNoOverflow();

        return $this->dependencyInstalled()
            ? Period::create($start, $end)
            : (object) [
                'startDate' => $start,
                'endDate' => $end,
            ];
    }

    /**
     * Provide fake analytics data for demo purposes.
     *
     * @param $period
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fakeData($period)
    {
        $data = collect([]);

        for ($date = Carbon::parse($period->startDate); $date->lte($period->endDate); $date->addDay()) {
            $data->push([
                'date' => Carbon::parse($date),
                'visitors' => rand(100, 1000),
                'pageViews' => rand(100, 1000),
            ]);
        }

        return $data;
    }
}
