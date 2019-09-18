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
    @stack('scaffold.headjs')
</head>
<body>
<div id="app">
    <header>
        <div class="headerpanel">
            <div class="logopanel">
                <h4 style="margin-top: 4px; text-align: center">
                    <a class="btn-quirk" href="{{ url(config('administrator.home_page') ?: route('scaffold.dashboard')) }}">
                        {!! $config->get('title') !!}
                    </a>
                </h4>
            </div>

            <div class="headerbar">
                @if ($config->get('search'))
                    <div class="searchpanel">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
                        </span>
                        </div><!-- input-group -->
                    </div>
                @endif

                <div class="header-right">
                    <ul class="headermenu">
                        @include(\Terranet\Administrator\Architect::template()->menu('tools'))
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <?php
    $user = auth('admin')->user();
    $pict = asset('/admin/images/admin.png');
    ?>

    <div class="leftpanel">
        <div class="leftpanelinner">
            <div class="tab-content">
                <div class="tab-pane active">
                    @include('administrator::menus.sidebar')
                </div>
            </div>
        </div>
    </div>

    <div class="mainpanel">
        <div class="contentpanel">
            @if (isset($resource))
                @yield('scaffold.create')

                <h4 class="btn-quirk">{{ $resource->title() }} @yield('total')</h4>
                @if ($config->get('breadcrumbs') && $breadcrumbs = $resource->breadcrumbs())
                    {!! $breadcrumbs->render() !!}
                @else
                    <br><br>
                @endif
            @endif

            @include(\Terranet\Administrator\Architect::template()->partials('messages'))

            @yield('scaffold.cards')

            @yield('scaffold.filter')

            @yield('scaffold.batch')

            @yield('scaffold.content')
        </div>
    </div>
</div>

@include('administrator::partials.scripts')
@stack('scaffold.js')
</body>
</html>
