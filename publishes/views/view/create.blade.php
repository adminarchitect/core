@unless($resource->actions()->readonly())
    <div class="btn-group pull-right mt10">
        @foreach($resource->actions()->actions()->authorized(auth('admin')->user(), $item) as $action)
            @unless ($action->hideFromView())
                {!! $action->renderBtn($item) !!}
            @endunless
        @endforeach

        @if ($resource->actions()->authorize('update', $item))
            <a href="{{ route('scaffold.edit', ['module' => $resource, 'id' => $item->getKey()]) }}"
               class="btn btn-primary btn-quirk">
                <i class="fa fa-pencil"></i>
            </a>
        @endif
        @if ($resource->actions()->authorize('delete', $item))
            <a href="{{ route('scaffold.delete', ['module' => $resource, 'id' => $item->getKey()]) }}"
               class="btn btn-danger btn-quirk"
               onclick="return confirm('Are you sure?');">
                <i class="fa fa-trash"></i>
            </a>
        @endif
    </div>
@endunless
