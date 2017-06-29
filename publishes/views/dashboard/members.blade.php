<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">{{ trans('administrator::dashboard.members.header') }}</h4>
    </div>

    <div class="panel-body">
        <table class="table table-striped-col">
            <tr>
                <td>{{ trans('administrator::dashboard.members.header_signed_last_week') }}:</td>
                <td width="40%">{{ $signed['lastWeek'] }}</td>
            </tr>
            <tr>
                <td>{{ trans('administrator::dashboard.members.header_signed_last_month') }}:</td>
                <td width="40%">{{ $signed['lastMonth'] }}</td>
            </tr>
            <tr>
                <td>{{ trans('administrator::dashboard.members.header_total') }}:</td>
                <td width="40%">{{ $total }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">{{ trans('administrator::dashboard.members.header_members_per_day') }}</h4>
    </div>
    <div class="panel-body">
        <table class="table table-striped-col">
            @foreach($signedStatistics as $date => $count)
                <tr>
                    <td>{{ $date }}</td>
                    <td width="40%">{{ $count }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
