@unless($resource->actions()->readonly())
    @if ($item && $item->exists)
        <div class="btn-group pull-right mt10">
            @if ($resource->actions()->authorize('view', $item))
                <a href="{{ route('scaffold.view', ['module' => $resource, 'id' => $item->getKey()]) }}"
                   class="btn btn-primary btn-quirk">
                    <i class="fa fa-eye"></i>
                </a>
            @endif

            @if ($resource->actions()->authorize('create'))
                <a href="{{ route('scaffold.create', ['module' => $resource]) }}"
                   class="btn btn-primary btn-quirk">
                    <i class="fa fa-plus"></i>
                </a>
            @endif

            @if ($resource->actions()->authorize('delete', $item))
                <a href="{{ route('scaffold.delete', ['module' => $resource, 'id' => $item->getKey()]) }}"
                   class="btn btn-danger btn-quirk"
                   onclick="return confirm('Are you sure?');">
                    <i class="fa fa-trash-o"></i>
                </a>
            @endif
        </div>
    @endif
@endunless
