@if ($title)
    @if ($module)
        <a href="{{ route('scaffold.view', ['module' => $module->url(), $related->getKeyName() => $related->getKey()]) }}"
           title="{{ $title }}"
        >
            {{ \Illuminate\Support\Str::limit($title, 25) }}
        </a>
    @else
        <span title="{{ $title }}">
            {{ \Illuminate\Support\Str::limit($title, 25) }}
        </span>
    @endif
@endif
