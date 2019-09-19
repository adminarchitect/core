@if (!$searchable)
    @include('administrator::fields.enum.edit')
@endif

@if ($searchable && $searchIn)
    <instant-search
            name="{{ $field->name() }}"
            data-url="/cms/search/?searchable={{ $searchIn }}&field={{ $searchBy }}"
            default-value="{{ (int) optional($field->value())->getKey() }}"
    ></instant-search>
@endif
