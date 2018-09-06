@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        @php($attributes = [
            'class' => 'form-control',
            'style' => 'min-width: 700px; height: 150px;',
            'data-editor' => $dataEditor ?? ''
        ])
        {!! Form::textarea($field->name(), $field->value(), $attributes) !!}
    @endslot
@endcomponent
