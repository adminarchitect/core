{!! Form::hidden($field->name(), 0, ['id' => Form::getIdAttribute($field->id(), []).'_hidden']) !!}
{!! Form::checkbox($field->name(), 1) !!}