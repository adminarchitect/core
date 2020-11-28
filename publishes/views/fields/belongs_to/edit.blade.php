@if (!$searchable)
    {!! Form::select($field->name(), $options, optional($field->value())->getKey(), $attributes) !!}
@endif

@if ($searchable && $searchIn)
    <instant-search
            name="{{ $field->name() }}"
            data-url="/cms/search/?searchable={{ $searchIn }}&field={{ $searchBy }}"
            default-value="{{ (int) optional($field->value())->getKey() }}"
    ></instant-search>
@endif
