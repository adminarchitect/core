@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input', Form::text($field->name(), $field->value(), ['class' => 'form-control', 'style' => 'width: 250px;']))
@endcomponent