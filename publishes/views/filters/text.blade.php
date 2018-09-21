@if (!empty($modes))
    <div class="input-group">
        @php($modeName = $field->name() . '_mode')
        <div class="input-group-addon" style="padding: 0 8px;">
            {!! Form::select($modeName, $modes, request($modeName)) !!}
        </div>
        {{ Form::text($field->name(), $field->value(), ['class' => 'form-control']) }}
    </div>
@else
    {{ Form::text($field->name(), $field->value(), ['class' => 'form-control']) }}
@endif
