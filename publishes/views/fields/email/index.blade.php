@if ($value = $field->value())
    <a href="mailto:{{$value}}" target="_blank" title="{{ $value }}">
        {{ str_limit($value, 25) }}
    </a>
@endif