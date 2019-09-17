@if ($resource->actions()->authorize('view', $item))
    <a data-scaffold-action="{{ $resource }}-view"
       href="{{ route('scaffold.view', ['module' => $resource, 'id' => $item->getKey()]) }}">
        <i class="fa fa-eye" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif
@if ($resource->actions()->authorize('update', $item))
    <a data-scaffold-action="{{ $resource }}-edit"
       href="{{ route('scaffold.edit', ['module' => $resource, 'id' => $item->getKey()]) }}">
        <i class="fa fa-pencil-square-o" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif
@if ($resource->actions()->authorize('delete', $item))
    <a data-scaffold-action="{{ $resource }}-delete"
       onclick="return confirm('{{ trans('administrator::messages.confirm_deletion') }}');"
       href="{{ route('scaffold.delete', ['module' => $resource, 'id' => $item->getKey()]) }}">
        <i class="fa fa-trash-o" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif

@if ($customActions = $resource->actions()->actions()->authorized(auth('admin')->user(), $item))
    <ul class="list-unstyled">
        @foreach($customActions as $action)
            @unless ($action->hideFromIndex())
            <li>
                {!! $action->render($item) !!}
            </li>
            @endunless
        @endforeach
    </ul>
@endif
