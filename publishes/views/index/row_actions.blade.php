@if ($actions->authorize('view', $item))
    <a data-scaffold-action="{{ $module }}-view"
       href="{{ route('scaffold.view', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
        <i class="fa fa-eye" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif
@if ($actions->authorize('update', $item))
    <a data-scaffold-action="{{ $module }}-edit"
       href="{{ route('scaffold.edit', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
        <i class="fa fa-pencil-square-o" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif
@if ($actions->authorize('delete', $item))
    <a data-scaffold-action="{{ $module }}-delete"
       onclick="return confirm('{{ trans('administrator::messages.confirm_deletion') }}');"
       href="{{ route('scaffold.delete', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
        <i class="fa fa-trash-o" style="font-size: 1.3em;"></i>&nbsp;
    </a>
@endif
@if ($customActions = $actions->actions()->authorized(auth('admin')->user(), $item))
    <ul class="list-unstyled">
        @foreach($customActions as $action)
            <li>
                {!! $action->render($item) !!}
            </li>
        @endforeach
    </ul>
@endif