<div class="input-group">
    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
    {{ Form::text($field->name(), $field->value(), ['class' => 'form-control', 'autocomplete' => 'off', 'data-filter-type' => 'date']) }}
</div>
