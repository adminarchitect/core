@if (!empty($value = $field->value()))
    {{ \Illuminate\Support\Str::limit(strip_tags($value), 200) }}
@endif
