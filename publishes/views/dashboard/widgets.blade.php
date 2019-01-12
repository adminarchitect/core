@foreach($widgets as $section)
    <section class="row">
        @foreach($section as $panel)
            <section class="col-md-{{ $panel->width() }}">
                {!! $panel->render() !!}
            </section>
        @endforeach
    </section>
@endforeach