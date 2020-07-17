<span style="color: {{ ($isTrue ? 'green' : 'inherit') }}">
    <i class="fa fa-circle{{ ($isTrue ? '' : '-thin') }}"></i>&nbsp;
    {{ ($isTrue ? trans('administrator::buttons.true') : trans('administrator::buttons.false')) }}
</span>
