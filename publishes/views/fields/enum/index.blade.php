<span {!! ($color ? "class=\"label\" style=\"background-color: $color\"" : "" ) !!} >
    {{ ($field->value() ? \Illuminate\Support\Arr::get($field->options, $field->value()) : '---') }}
</span>
