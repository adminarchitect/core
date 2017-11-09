## Dashboard

![Admin Architect - Dashboard](http://docs.adminarchitect.com/images/dashboard/dashboard.jpg)

Dashboard probably is the most-visited by administrators page of every admin panel! 
And Admin Architect doesn't want to be an exception.

The main dashboard template is located at `resources/views/vendor/administrator/layouts/dashboard.blade.php`

You decide the number of widgets, their position and their content...

It may be a simple Counter or a complex Graph - it doesn't mater...

Admin Architect provides an easy way to add new widget:

```bash
php artisan administrator:panel Overview
```

Where the `Overview` panel is a simple class that implements `Widgetable` contract with the single method `render()`:

```php
class Overview implements Widgetable
{
    use Stringify;

    /**
     * Widget contents
     *
     * @return mixed string|View
     */
    public function render()
    {
        return view('admin.dashboard.overview', [
            'title'   => 'Audience &raquo; Overview',
            'total'   => $total = User::count(),
            'writers' => $writers = User::has('articles')->count(),
            'active'  => $active = User::active()->count(),
            'signed'  => [
                'lastWeek'  => User::signedWeeksAgo(1)->count(),
                'lastMonth' => User::signedMonthsAgo(1)->count()
            ],
            'ratio' => [
                'writers' => ($total ? round(($writers / $total) * 100) : 0),
                'active'  => ($total ? round(($active / $total) * 100) : 0),
            ]
        ]);
    }
}
```

The contents of view is too long for placing here, we just will show the result you may see.

![Admin Architect - Dashboard - Overview panel](http://docs.adminarchitect.com/images/dashboard/overview.jpg)

Another example:

```bash
php artisan administrator:panel Registrations
```

```php
class Registrations implements Widgetable
{
    use Stringify;

    /**
     * Widget contents
     *
     * @return mixed string|View
     */
    public function render()
    {
        return view('admin.dashboard.registrations')->with([
            'title' => 'Audience &raquo; Registrations',
            'data'  => $this->dailyStats()
        ]);
    }

    protected function dailyStats()
    {
        return User::signedMonthsAgo(3)
            ->select([
				DB::raw('COUNT(id) AS cnt'),
				DB::raw('DATE(created_at) as dt')
			])
            ->groupBy('dt')->lists('cnt', 'dt')
            ->toArray();
    }
}
```

One thing you need to do, is to register your dashboard panel in Dashboard factory:

```php
### \App\Http\Terranet\Administrator\Dashboard\Factory
protected function registerPanels()
{
    $this->dashboard
        ->row(function (DashboardRow $row) {
        	# consider Bootstrap 12 columns grid
        	$row->panel(new Overview)->setWidth(12);
        })
        ->row(function(DashboardRow $row) {
            $row->panel(new Registration)->setWidth(12);
        });

    return $this->dashboard;
}
```

![Admin Architect - Dashboard -> Registrations Panel](http://docs.adminarchitect.com/images/dashboard/registrations.jpg)

There is no limits how many widgets (panels) you can use in Dashboard and how to style them.