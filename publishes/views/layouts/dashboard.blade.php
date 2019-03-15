@extends($template->layout())

@inject('template', 'scaffold.template')

@section('scaffold.content')
    @include('administrator::dashboard.widgets', ['widgets' => app('scaffold.dashboard')])
@endsection
