@php($attributes = [
    'class' => 'form-control',
    'data-editor' => $dataEditor ?? ''
])
{!! Form::textarea($field->name(), $field->value(), $attributes) !!}
