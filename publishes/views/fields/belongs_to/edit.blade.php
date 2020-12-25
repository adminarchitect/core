@if (!$searchable)
    {!! Form::select($field->name(), $options, optional($field->value())->getKey(), $attributes) !!}
@endif

@if ($searchable && $searchIn)
    <instant-search
        name="{{ $field->name() }}"
        data-url="{{ $searchUrl }}"
        default-value="{{ optional($field->value())->getKey() }}"
    ></instant-search>
@endif
