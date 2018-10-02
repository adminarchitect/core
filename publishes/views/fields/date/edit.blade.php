<date-time-picker
        type="date"
        name="{{ $field->name() }}"
        default-value="{{ Carbon\Carbon::parse($field->value())->toDateTimeString() }}"
></date-time-picker>