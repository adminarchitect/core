@inject('template', 'scaffold.template')
@inject('widgets', 'scaffold.dashboard')

@extends($template->layout())

@section('scaffold.content')
    @include('administrator::dashboard.widgets', ['widgets' => $widgets])
@endsection
