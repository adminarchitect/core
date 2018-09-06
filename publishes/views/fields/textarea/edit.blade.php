@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        {!! Form::textarea($field->name(), $field->value(), ['class' => 'form-control', 'style' => 'min-width: 700px; height: 150px;']) !!}
    @endslot
@endcomponent
