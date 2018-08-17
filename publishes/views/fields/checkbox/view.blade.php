@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input')
        <span>{!! $options[$field->value()] !!}</span>
    @endslot
@endcomponent
