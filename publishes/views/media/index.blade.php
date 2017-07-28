@inject('template', 'scaffold.template')
@inject('config', 'scaffold.config')

@extends($template->layout())

@section('scaffold.js')
    <script src="{{ mix('admin/media1.js') }}"></script>
@endsection

@section('scaffold.content')
    <script>
        window.mediaFiles = {!! json_encode($files) !!};
        window.XSRF_TOKEN = '{{ csrf_token() }}';
        window.UPLOADER_URL = '{{ route('scaffold.media.upload') }}';
        window.REQUEST_PATH = '{{ request('path', '') }}';
    </script>
    <h4>{{ trans('administrator::media.title') }}</h4>

    {!! $breadcrumbs !!}

    <div id="media">
        <media-manager></media-manager>
    </div>
@append
