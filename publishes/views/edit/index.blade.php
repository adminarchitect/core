@inject('form', 'scaffold.form')
@inject('module', 'scaffold.module')
@inject('actions', 'scaffold.actions')
@inject('template', 'scaffold.template')

@extends($template->layout())

@section('scaffold.create')
    @include($template->edit('create'))
@endsection

@section('scaffold.content')
    <div class="panel">
        <div class="panel-body">
            {!! Form::model(isset($item) ? $item : null, ['method' => 'post', 'files' => true]) !!}
            <table class="table table-striped-col">
                @each($template->edit('row'), $form, 'field')

                @unless($actions->readonly())
                    @include($template->edit('actions'))
                @endunless
            </table>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@include($template->edit('scripts'))
