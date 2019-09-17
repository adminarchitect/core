@extends($resource->template()->layout())

@section('module_header')
    {{ $resource->title() }}
@stop

@section('scaffold.content')
    @php($form = $resource->form())
    {!! Form::open() !!}
    <table class="table">
        @foreach($form as $field)
            <tr>
                <td style="width: 20%; min-width: 200px;">
                    {!! Form::label($field->name(), $field->title()) !!}:
                    @if ($description = $field->getDescription())
                        <p class="small">{!! $description !!}</p>
                    @endif
                </td>
                <td>
                    {!! $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" class="text-center">
                <input type="submit" name="save" value="{{ trans('administrator::buttons.save') }}"
                       class="btn btn-primary btn-block"/>
            </td>
        </tr>
    </table>

    {!! Form::close() !!}
@stop
