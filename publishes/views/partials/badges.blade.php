@inject('badges', 'scaffold.badges')

<ul class="nav navbar-nav">
    @foreach(app('scaffold.badges') as $badge)
        <li class="dropdown messages-menu notifications-menu tasks-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="{{ $badge->icon() }}"></i>
                @if ($c = $badge->count())
                    <span class="{{ $badge->status() }}">{{ $c }}</span>
                @endif
            </a>
            <ul class="dropdown-menu">
                <li class="header">{{ $badge->message() }}</li>
                <li>
                    <ul class="menu">
                        @include($badge->template(), ['collection' => $badge->collection()->items()])
                    </ul>
                </li>
                @if ($link = $badge->linkTo())
                    <li class="footer">
                        {!! $link !!}
                    </li>
                @endif
            </ul>
        </li><!-- /.messages-menu -->
    @endforeach
</ul>
