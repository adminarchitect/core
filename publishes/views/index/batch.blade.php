@if (count($batch = $actions->batch()->authorized(auth('admin')->user())))
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            {{ trans('administrator::buttons.batch_actions') }}
            <span class="fa fa-caret-down"></span>
        </button>
        <ul class="dropdown-menu batch-actions">
            @foreach($batch as $action)
                <li>{!! $action->render() !!}</li>
            @endforeach
        </ul>
    </div>
@endif

