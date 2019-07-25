@extends($template->layout())

@inject('module', 'scaffold.module')
@inject('template', 'scaffold.template')

@php($actions = $module->actionsManager())

@section('scaffold.create')
    @include($template->edit('create'))
@endsection

@section('scaffold.content')
    @php($form = $module->form())
    <div class="panel">
        <div class="panel-body">
            @php($form->each->setModel($item))

            {!! Form::model($item, ['method' => 'post', 'files' => true]) !!}
            @foreach($form->visibleOnPage(\Terranet\Administrator\Scaffolding::PAGE_EDIT)->filter(function($item) {
                return $item instanceof \Terranet\Administrator\Field\Hidden;
            }) as $hidden)
                {!! $hidden->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
            @endforeach
            <table class="table table-striped-col">
                @each($template->edit('row'), $form->visibleOnPage(\Terranet\Administrator\Scaffolding::PAGE_EDIT), 'field')

                @unless($actions->readonly())
                    @include($template->edit('actions'))
                @endunless
            </table>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@include($template->edit('scripts'))
