@unless($resource->actions()->readonly())
@section('scaffold.create')
    <div class="btn-group pull-right mt10">
        @if ($resource->actions()->authorize('create'))
            <a href="{{ route('scaffold.create', ['module' => $resource]) }}"
               class="btn btn-success btn-quirk">
                <i class="fa fa-plus"></i>
                {{ trans('administrator::buttons.create') }}
            </a>
        @endif
    </div>
@endsection
@endunless
