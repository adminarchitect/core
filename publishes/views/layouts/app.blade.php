@inject('config', 'scaffold.config')
@inject('breadcrumbs', 'scaffold.breadcrumbs')
@inject('module', 'scaffold.module')
@inject('navigation', 'scaffold.navigation')
<!DOCTYPE html>
<html lang="en">
<head ng-app="Architector">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>
        {{ strip_tags($config->get('title')) }}
        @if ($module && ($title = $module->title()))
        &raquo; {{ $title }}
        @endif
    </title>

    @include('administrator::partials.styles')

    <!--[if lt IE 9]>
{{--    <script src="{{ asset('admin/lib/html5shiv/html5shiv.js') }}"></script>--}}
{{--    <script src="{{ asset('admin/lib/respond/respond.src.js') }}"></script>--}}
    <![endif]-->
    @yield('scaffold.css')

    @yield('scaffold.headjs')
</head>
<body>
<header>
    <div class="headerpanel">
        <div class="logopanel">
            <h2>
                <a href="{{ url(config('administrator.home_page') ?: route('scaffold.dashboard')) }}">{!! $config->get('title') !!}</a>
            </h2>
        </div>

        <div class="headerbar">
            <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>

            <div class="header-right">

                <ul class="headermenu">
                    @include($template->menu('tools'))
                </ul>
            </div>
        </div>
    </div>
</header>

<?php
$user = auth('admin')->user();
$pict = asset('/images/admin.png');
?>

<div class="leftpanel">
    <div class="leftpanelinner">
        <div class="media leftpanel-profile">
            <div class="media-left">
                @if (app('scaffold.config')->get('gravatar', true) && Gravatar::exists($user->email))
                    <img height="48" width="48" src="{{ Gravatar::get($user->email, ['size' => 160, 'fallback' => $pict]) }}" alt="{{ $user->name }}" class="media-object img-circle"/>
                @else
                    <img height="48" width="48" src="{{ $pict }}" class="media-object img-circle" alt="{{ $user->name }}">
                @endif
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{ auth('admin')->user()->name }}</h4>
                <span>Joined {{ auth('admin')->user()->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane active">
                @include('administrator::menus.sidebar')
            </div>
        </div>
    </div>
</div>

<div class="mainpanel">
    <div class="contentpanel">
        @if ($module)
            @yield('scaffold.create')

            @if ($breadcrumbs)
                <h4>{{ $module->title() }} @yield('total')</h4>
                {!! $breadcrumbs->render() !!}
            @endif
        @endif

        @include($template->partials('messages'))

        @yield('scaffold.filter')

        @yield('scaffold.batch')

        @yield('scaffold.content')
    </div>
</div>

@include('administrator::partials.scripts')

@yield('scaffold.js')
</body>
</html>
