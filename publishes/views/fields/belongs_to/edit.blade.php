@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        <instant-search
                data-url="/cms/search/?searchable={{ \App\User::class }}&field=name"
                default-value="{{ request('user_id') }}"
        ></instant-search>
    @endslot
@endcomponent