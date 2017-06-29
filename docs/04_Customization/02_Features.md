## Dashboard

![Overview](http://docs.adminarchitect.com/docs/images/dashboard/dashboard.jpg)

Dashboard probably is the most-visited by administrators page of every admin panel! And Admin Architect doesn't want to be an exception.

The main dashboard template is located at `resources/views/vendor/administrator/layouts/dashboard.blade.php`

You decide the number of widgets, their position and their content (Bootstrap based layout will help you to do this)...

It may be a simple Counter or a complex Graph - it doesn't mater...

Admin Architect provides an easy way to add new widget:

```
php artisan administrator:panel Overview
```
Where the `Overview` panel is a simple class that implements `Widgetable` contract with the single method `render()`:

```
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

![Overview](http://docs.adminarchitect.com/docs/images/dashboard/overview.jpg)


Another example:

```
php artisan administrator:panel Registrations
```

```
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

```

    <div class="box no-border">
        <div class="box-header">
            <h3 class="less">{{ $title }}</h3>
        </div>
        <div class="box-body">
            <canvas id="signup-chart" style="width: 100%; height: 300px;"></canvas>
        </div>
    </div>

    @section('scaffold.js')
        <script>
            $(function () {
                var ctx = $("#signup-chart").get(0).getContext("2d");

                var data = {
                    labels: ['{!! join("', '", array_keys($data)) !!}'],
                    datasets: [
                        {
                            fillColor: "rgba(151,187,205,0.2)",
                            strokeColor: "rgba(151,187,205,1)",
                            data: [ {!! join(',', $data) !!} ]
                        }
                    ]
                };

                new Chart(ctx).Line(data, {});
            })
        </script>
    @append

```

![Overview](http://docs.adminarchitect.com/docs/images/dashboard/registrations.jpg)

After all widgets done, your `dashboard` layout may look like:

```
@inject('template', 'scaffold.template') // Template instance

@inject('overview', 'App\Http\Terranet\Administrator\Dashboard\Overview')
@inject('registrations', 'App\Http\Terranet\Administrator\Dashboard\Registrations')
@inject('age', 'App\Http\Terranet\Administrator\Dashboard\UserAge')
@inject('gender', 'App\Http\Terranet\Administrator\Dashboard\UserGender')
@inject('health', 'App\Http\Terranet\Administrator\Dashboard\UserHealth')

@extends($template->layout())

@section('scaffold.js')
   	<script src="{{ asset('/administrator/plugins/chartjs/Chart.min.js') }}"></script>
@append

@section('scaffold.content')
    <section>{!! $overview !!}</section>

    <section>{!! $registrations !!}</section>

    <div class="row">
        <div class="col-lg-4">
            {!! $age !!}
        </div>
        <div class="col-lg-4">
            {!! $gender !!}
        </div>
        <div class="col-lg-4">
            {!! $health !!}
        </div>
    </div>
@endsection

```


There is no limits how many widgets (panels) you can use in Dashboard and how to style them.

Admin Architect was built on top of [AdminLTE](https://almsaeedstudio.com/themes/AdminLTE/index.html), so you can use it as a Guide.


## Templates

Sometimes, while developing complex applications, you'll need to change the default layout or partial view: Maybe you'll want to inline javascript, of add dynamic behavior for index page, or your edit/create form requires custom javascript libraries for more customization...

Admin Architect comes with a solution called `Template`: All rendered pages are separated in a `replaceable` blocks.

Template is registered per Resource, so if you want to customise your `index` view for `Users` resource, just create new template:

```
php artisan administrator:template Users
```

```
class Users extends Template implements TemplateProvider
{
    /**
     * Scaffold index templates
     *
     * @param $partial
     * @return mixed array|string
     */
    public function index($partial = 'index')
    {
        $partials = array_merge(
            parent::index(null),
            [
                'index' => 'admin.users.custom_index'
            ]
        );

        return (null === $partial ? $partials : $partials[$partial]);
    }
}
```

Admin Architect will search for a view `resources/views/admin/users/custom_index.blade.php` for index screen.

For more details please checkout the Template class: `Terranet\Administrator\Services\Template`;

## Breadcrumbs

![Overview](http://docs.adminarchitect.com/docs/images/index/breadcrumbs.jpg)

To build breadcrumbs Admin Architect uses [Laravel Breadcrumbs](https://github.com/davejamesmiller/laravel-breadcrumbs) package.

Please checkout its documentation if you have any questions.

As like as Template you are free to change the way how breadcrumbs are rendered by replacing default Breadcrumbs instance by your own:

```
php artisan administrator:breadcrumbs Users
```