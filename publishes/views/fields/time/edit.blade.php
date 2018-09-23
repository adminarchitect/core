@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        <date-time-picker
                type="time"
                name="{{ $field->name() }}"
                default-value="{{ Carbon\Carbon::parse($field->value())->toDateTimeString() }}"
        ></date-time-picker>
    @endslot
@endcomponent