@inject('template', 'scaffold.template')
@inject('dashboard', 'scaffold.dashboard')

@extends($template->layout())

@section('scaffold.content')
    @foreach($dashboard as $section)
        <section class="row">
            @foreach($section as $panel)
                <section class="col-md-{{ $panel->width() }}">
                    {!! $panel !!}
                </section>
            @endforeach
        </section>
    @endforeach
@endsection
