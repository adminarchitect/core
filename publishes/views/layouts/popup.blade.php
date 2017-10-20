<!DOCTYPE html>
<html lang="en">
<head ng-app="Architector">
    <title>{{ trans('administrator::media.title') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('administrator::partials.styles')

    @stack('scaffold.css')
    @stack('scaffold.headjs')
</head>
<body>
<div class="contentpanel" style="padding-top:10px;">
    @yield('scaffold.content')
</div>

@include('administrator::partials.scripts')
@stack('scaffold.js')
</body>
</html>
