<date-time-picker
        type="datetime"
        name="{{ $field->name() }}"
        default-value="{{ Carbon\Carbon::parse($field->value())->toDateTimeString() }}"
></date-time-picker>