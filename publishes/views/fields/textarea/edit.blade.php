@php($attributes = [
    'class' => 'form-control',
    'style' => 'min-width: 700px; height: 150px;',
    'data-editor' => $dataEditor ?? ''
])
{!! Form::textarea($field->name(), $field->value(), $attributes) !!}
