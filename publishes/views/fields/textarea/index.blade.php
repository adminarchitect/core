@if (!empty($value = $field->value()))
    {{ str_limit(strip_tags($value), 200) }}
@endif