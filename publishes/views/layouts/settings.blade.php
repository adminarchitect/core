@extends($template->layout())

@inject('module', 'scaffold.module')
@inject('template', 'scaffold.template')

@section('module_header')
    {{ app('scaffold.module')->title() }}
@stop

@section('scaffold.content')
    @php($form = $module->form())
    {!! Form::open() !!}
    <table class="table">
        @foreach($form as $field)
        <?php $input = $field->getInput(); ?>
        <tr {{ $input->hasErrors() ? 'class="has-error"' : '' }}>
            <td style="width: 20%; min-width: 200px;">
                {!! Form::label($input->getName(), $field->title()) !!}:
                @if ($description = $field->getDescription())
                    <p class="small">{!! $description !!}</p>
                @endif
            </td>
            <td>
                <?php $input->setValue(options_find($input->getName())); ?>
                {!! $input->html() !!}
            </td>
        </tr>
        @endforeach

        <tr>
            <td colspan="2" class="text-center">
                <input type="submit" name="save" value="{{ trans('administrator::buttons.save') }}" class="btn btn-primary btn-block" />
            </td>
        </tr>
    </table>

    {!! Form::close() !!}
@stop