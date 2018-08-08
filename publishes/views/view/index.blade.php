@inject('template', 'scaffold.template')
@inject('module', 'scaffold.module')
@inject('actions', 'scaffold.actions')

@extends($template->layout())

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
@append