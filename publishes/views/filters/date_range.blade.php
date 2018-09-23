<?php
[$from, $to] = array_values($field->value() && is_array($field->value())
    ? $field->value()
    : ['from' => '', 'to' => '']
);
?>
<date-time-picker
        type="daterange"
        :name="['{{ $field->name() }}[from]', '{{ $field->name() }}[to]']"
        @if ($from && $to)
        :default-value="['{{ $from }}', '{{ $to }}']"
        @endif
></date-time-picker>
