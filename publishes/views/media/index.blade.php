@inject('template', 'scaffold.template')
@inject('config', 'scaffold.config')

@extends($template->layout())

@prepend('scaffold.headjs')
    <script>
        window.mediaFiles = {!! json_encode($files) !!};
        window.XSRF_TOKEN = '{{ csrf_token() }}';
        window.UPLOADER_URL = '{{ route('scaffold.media.upload') }}';
        window.REQUEST_PATH = '{{ request('path', '') }}';
    </script>
@endprepend

@section('scaffold.content')
    <h4>{{ trans('administrator::media.title') }}</h4>

    {!! $breadcrumbs !!}

    <media-manager></media-manager>
@append
