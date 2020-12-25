@inject('config', 'scaffold.config')

    <!DOCTYPE html>
<html lang="en">
<head ng-app="Architector">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ strip_tags($config->get('title')) }}
        @if (isset($resource) && ($title = $resource->title()))
            &raquo; {{ $title }}
        @endif
    </title>

    @include('administrator::partials.styles')
    @stack('scaffold.css')
    <link rel="stylesheet" href="{{ mix('print.css', 'admin') }}">
</head>
<body style="background: white">
<div id="app">
    <div class="contentpanel">
        @yield('scaffold.content')
    </div>
</div>
</body>
</html>
