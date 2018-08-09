@php($value = $field->value())
@if ($value->exists())
    <a href="{{ $value->url() }}" target="_blank" title="{{ $value->originalFilename() }}">
        {{ trans('administrator::buttons.download') }}
    </a>
@endif