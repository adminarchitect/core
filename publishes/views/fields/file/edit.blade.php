@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input')
        @if ($attachment->exists())
            <a href="{{ $attachment->url() }}" target="_blank">
                <i class="fa fa-cloud-download"></i>&nbsp;{{ $attachment->originalFilename() }}
            </a>

            <div style="margin-top: 10px;">
                <a href="{{ route('scaffold.delete_attachment', [
                    'module' => app('scaffold.module'),
                    'attachment' => $field->name(),
                    'id' => $model->getKey(),
                ]) }}" class="btn btn-danger" style="padding: 4px 26px;" onclick="return confirm('Are you sure?');">
                    <i class="fa fa-trash"></i>&nbsp;{{ trans('administrator::buttons.delete') }}
                </a>
            </div>
        @else
            {!! Form::file($attachment->name(), []) !!}
        @endif
    @endslot
@endcomponent