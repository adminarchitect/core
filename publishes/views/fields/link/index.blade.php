@if ($value = $field->value())
    <a href="{{$value}}" target="_blank" title="{{ $value }}">
        {{ \Illuminate\Support\Str::limit($value, 25) }}
    </a>
@endif
