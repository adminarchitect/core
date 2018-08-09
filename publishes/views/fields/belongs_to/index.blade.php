@if ($title)
    @if ($module)
        <a href="{{ route('scaffold.view', ['module' => $module->url(), $relation->getKeyName() => $relation->getKey()]) }}"
           title="{{ $title }}"
        >
            {{ str_limit($title, 25) }}
        </a>
    @else
        <span title="{{ $title }}">
            {{ str_limit($title, 25) }}
        </span>
    @endif
@endif