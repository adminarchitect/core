<div class="panel panel-site-traffic">
    <div class="panel-heading">
        <h4 class="panel-title">Audience Overview / {{ $period->startDate->toDateString() }} - {{ $period->endDate->toDateString() }}</h4>
    </div>
    <div class="panel-body">
        <div class="row mb20">
            <div class="col-xs-6 col-sm-4">
                <div class="pull-left">
                    <div class="icon icon-blue">
                        <i class="fa fa-area-chart"></i>
                    </div>
                </div>
                <div class="pull-left">
                    <h4 class="panel-title">Visitors</h4>
                    <h3>{{ $visitors }}</h3>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <div class="pull-left">
                    <div class="icon icon-light-blue">
                        <i class="fa fa-eye"></i>
                    </div>
                </div>
                <h4 class="panel-title">Pageviews</h4>
                <h3>{{ $pageViews }}</h3>
            </div>
            <div class="col-xs-6 col-sm-4">
                <div class="pull-left">
                    <div class="icon icon-green">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
                <h4 class="panel-title">Max Visitors</h4>
                <h3>{{ $maxVisitors }}</h3>
            </div>
        </div>

        <canvas id="dailyStats-chart" style="width: auto; max-height: 400px;"></canvas>
    </div>
</div>

@section('scaffold.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        $(function() {
            var data = {
                labels: ['{!! implode("', '", $labels) !!}'],
                datasets: [
                    {
                        label: 'Visitors',
                        backgroundColor: 'rgba(60,141,188,0.4)',
                        borderColor: 'rgba(60,141,188,1)',
                        borderWidth: 1,
                        pointRadius: 1,
                        data: [ {!! join(', ', $dailyStats->pluck('visitors')->toArray()) !!} ],
                    },
                    {
                        label: 'Page Views',
                        backgroundColor: 'rgba(34,45,50,0.4)',
                        borderColor: 'rgba(34,45,50,1)',
                        borderWidth: 1,
                        pointRadius: 1,
                        data: [ {!! join(', ', $dailyStats->pluck('pageViews')->toArray()) !!} ],
                    },
                ],
            };

            new Chart($('#dailyStats-chart'), {
                type: 'line',
                data: data,
                options: {
                    legend: {
                        display: false,
                    },
                    scales: {
                        xAxes: [
                            {
                                type: 'time',
                            },
                        ],
                    },
                },
            });
        });
    </script>
@append