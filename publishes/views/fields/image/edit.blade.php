@if ($attachment && $attachment->exists())
    <p>
        <a rel="image_{{ $field->name() }}"
           href="{{ $attachment->url() }}"
           class="fancybox"
           style="display: inline-block; text-align: center;">
            <img src="{{ $attachment->url() }}" style="max-width: 400px; max-height: 400px;" class="img-responsive"/>
        </a>
    </p>

    @foreach($attachment->variants() as $variant)
        <a href="{{ $attachment->url($variant) }}" class="fancybox" rel="image_{{ $field->name() }}">
            <span class="btn-quirk"><i class="fa fa-genderless"></i> {{ $variant }}</span>
            &nbsp;&nbsp;
        </a>
    @endforeach

    <div style="margin-top: 24px;">
        <a href="{{ route('scaffold.delete_attachment', [
                    'module' => app('scaffold.module'),
                    'attachment' => $field->name(),
                    'id' => $model->getKey(),
                ]) }}" class="btn btn-danger" style="padding: 4px 26px;" onclick="return confirm('Are you sure?');">
            <i class="fa fa-trash"></i>&nbsp;{{ trans('administrator::buttons.delete') }}
        </a>
    </div>
@else
    {!! Form::file($field->name(), []) !!}
@endif