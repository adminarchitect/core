@if ($resource->actions()->authorize('delete', $item))
    <div class="pull-right" style="margin-right: 15px; padding-top: 15px;">
        {!! link_to(route('scaffold.delete', ['module' => $resource, 'id' => $item->getKey()]), trans('administrator::buttons.delete'), ['class' => "btn btn-danger", 'onclick' => "return confirm('Are you sure?')"]) !!}
    </div>
@endif
@if ($resource->actions()->authorize('update', $item))
    <div class="pull-right" style="margin-right: 15px; padding-top: 15px;">
        {!! link_to(route('scaffold.edit', ['module' => $resource, 'id' => $item->getKey()]), trans('administrator::buttons.edit'), ['class' => "btn btn-warning"]) !!}
    </div>
@endif
