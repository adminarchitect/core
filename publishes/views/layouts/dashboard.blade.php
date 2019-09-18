@extends(\Terranet\Administrator\Architect::template()->layout())

@section('scaffold.content')
    @include('administrator::dashboard.widgets', ['widgets' => app('scaffold.dashboard')])
@endsection
