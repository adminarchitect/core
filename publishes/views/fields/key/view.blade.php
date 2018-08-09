@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input', $field->value())
@endcomponent