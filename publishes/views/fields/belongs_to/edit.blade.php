@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        <instant-search
                name="{{ $field->name() }}"
                data-url="/cms/search/?searchable={{ \App\User::class }}&field=name"
                default-value="{{ (int) (request('user_id') ?: optional($field->value())->getKey()) }}"
        ></instant-search>
    @endslot
@endcomponent