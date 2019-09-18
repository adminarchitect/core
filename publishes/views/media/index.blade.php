@extends(\Terranet\Administrator\Architect::template()->layout($popup ? 'popup' : 'app'))

@inject('config', 'scaffold.config')

@push('scaffold.headjs')
    @include('administrator::media.scripts')
@endpush

@section('scaffold.content')
    <h4>{{ trans('administrator::media.title') }}</h4>

    {!! $breadcrumbs !!}

    <div id="app">
        <media-manager></media-manager>
    </div>
@append
