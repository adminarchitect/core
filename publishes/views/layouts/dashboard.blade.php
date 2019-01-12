@inject('template', 'scaffold.template')

@extends($template->layout())

@section('scaffold.content')
    @include('administrator::dashboard.widgets', ['widgets' => app('scaffold.dashboard')])
@endsection
