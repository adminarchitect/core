<date-time-picker
        type="time"
        name="{{ $field->name() }}"
        default-value="{{ Carbon\Carbon::parse($field->value())->toDateTimeString() }}"
></date-time-picker>