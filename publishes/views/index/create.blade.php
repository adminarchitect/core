@unless($actions->readonly())
@section('scaffold.create')
    <div class="btn-group pull-right mt10">
        @if ($actions->authorize('create'))
            <a href="{{ route('scaffold.create', app('scaffold.magnet')->with(['module' => $module])->toArray()) }}"
               class="btn btn-success btn-quirk">
                <i class="fa fa-plus"></i>
                {{ trans('administrator::buttons.create') }}
            </a>
        @endif
    </div>
@endsection
@endunless
