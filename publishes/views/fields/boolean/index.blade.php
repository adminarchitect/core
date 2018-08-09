<span style="color: {{ ($value = $field->value() ? 'green' : 'inherit') }}">
    <i class="fa fa-circle{{ ($value ? '' : '-thin') }}"></i>&nbsp;
    {{ ($value ? trans('administrator::buttons.true') : trans('administrator::buttons.false')) }}
</span>