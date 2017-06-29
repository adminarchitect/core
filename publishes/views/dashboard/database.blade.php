<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">
            {{ trans('administrator::dashboard.database.header') }}
        </h4>
    </div>

    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <tr>
                <th>{{ trans('administrator::dashboard.database.header_engine') }}</th>
                <th>{{ trans('administrator::dashboard.database.header_rows') }}</th>
                <th>{{ trans('administrator::dashboard.database.header_index') }}</th>
                <th>{{ trans('administrator::dashboard.database.header_collation') }}</th>
            </tr>
            @foreach($dbStats as $table)
                <tr>
                    <td>
                        {{ $table->Name }}
                        <small style="color:#808080;">[{{ $table->Engine }}]</small>
                    </td>
                    <td>{{ $table->Rows }}</td>
                    <td>{{ round($table->Index_length/pow(1024, 2), 2) }} MB</td>
                    <td>{{ $table->Collation }}</td>
                </tr>
            @endforeach
        </table>
    </div>

</div>