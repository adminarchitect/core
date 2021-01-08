@if ('datalist' === $field->type)
    {!! Form::text($field->name(), $field->value(), ['id' => $field->id(), 'list' => "scaffold_{$field->id()}"] + $attributes) !!}
    {!! Form::datalist("scaffold_{$field->id()}", $field->options) !!}
@else
    {{ Form::select($field->name(), ($field->isArray ? [] : ['' => "&mdash;"]) + ($field->options ?? []), $field->value(), ['multiple' => $field->isArray] + $attributes) }}
@endif

