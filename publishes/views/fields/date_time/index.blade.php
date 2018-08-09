@php($color = str_contains($name, ['delete', 'destroy', 'erase', 'remove']) ? 'danger' : 'default')
<span class="label label-{{ $color }}">
    {{ $formatted }}
</span>