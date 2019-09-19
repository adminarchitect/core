<span {!! ($color ? "class=\"label\" style=\"background-color: $color\"" : "" ) !!} >
    {{ ($field->value() ? \Illuminate\Support\Arr::get($options, $field->value()) : '---') }}
</span>
