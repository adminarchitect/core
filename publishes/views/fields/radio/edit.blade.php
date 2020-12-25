@foreach($field->options as $optionValue => $optionLabel)
    <label for="{{ $id = \Illuminate\Support\Str::slug($field->id().'-'.$optionValue) }}" style="margin-right: 10px;">
        {!! Form::radio($field->name(), $optionValue, null, ['id' => $id]) !!}
        <span>{{ $optionLabel }}</span>
    </label>
@endforeach
