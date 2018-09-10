@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->title())
    @slot('input')
        {!! Form::select($field->name(), $options, $field->value(), ['class' => 'form-control']) !!}
    @endslot
@endcomponent