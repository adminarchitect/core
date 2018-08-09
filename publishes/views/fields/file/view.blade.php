@php($value = $field->value())
@if ($value->exists())
    <p>{{ $value->originalFilename() }}</p>
    <a href="{{ $value->url() }}" target="_blank">
        <i class="fa fa-cloud-download"></i>&nbsp;{{ trans('administrator::buttons.download') }}
    </a>
@endif
