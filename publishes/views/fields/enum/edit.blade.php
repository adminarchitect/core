@if ('datalist' === $field->type)
    {!! Form::text($field->name(), $field->value(), ['id' => $field->id(), 'class' => 'form-control', 'list' => "scaffold_{$field->id()}"]) !!}
    {!! Form::datalist("scaffold_{$field->id()}", $field->options) !!}
@else
    {{ Form::select($field->name(), $field->options ?? [], $field->value(), ['class' => 'form-control', 'multiple' => $field->isArray] + $field->getAttributes()) }}
@endif

