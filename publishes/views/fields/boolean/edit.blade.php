@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        {!! Form::hidden($field->name(), 0, ['id' => Form::getIdAttribute($field->id(), []).'_hidden']) !!}
        {!! Form::checkbox($field->name(), 1) !!}
    @endslot
@endcomponent