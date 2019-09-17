@extends($resource->template()->layout())

@php($actions = $resource->actions())

@section('scaffold.create')
    @include($resource->template()->view('create'))
@endsection

@section('scaffold.content')
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @include($resource->template()->view('model'), $item)
                </div>
            </div>
        </div>
    </div>

    @include('administrator::dashboard.widgets', ['widgets' => $resource->widgets()])
@append
