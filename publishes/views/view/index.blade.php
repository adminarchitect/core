@extends($template->layout())

@inject('template', 'scaffold.template')
@inject('module', 'scaffold.module')

@php($actions = $module->actionsManager())

@section('scaffold.create')
    @include($template->view('create'))
@endsection

@section('scaffold.content')
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @include($template->view('model'), $item)
                </div>
            </div>
        </div>
    </div>

    @include('administrator::dashboard.widgets', ['widgets' => app('scaffold.widgets')])
@append
