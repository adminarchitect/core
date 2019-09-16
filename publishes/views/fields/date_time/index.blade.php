@php($color = \Illuminate\Support\Str::contains($field->id(), ['delete', 'destroy', 'erase', 'remove']) ? 'danger' : 'default')
<span class="label label-{{ $color }}">
    {{ $formatted }}
</span>
