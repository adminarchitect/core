@if ($count)
    <a href="{{ route('scaffold.view', ['module' => $module, 'id' => $model->getKey()]) }}">
        <span class="label label-default">
            <i class="fa fa-picture-o"></i>&nbsp;{{$count}}
        </span>
    </a>
@endif