@if ($breadcrumbs)
    <ol class="breadcrumb breadcrumb-quirk">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <li>
                    <a href="{{ $breadcrumb->url }}">
                        @if ($loop->first)
                            <i class="fa fa-home mr5"></i>
                        @endif
                        {{ $breadcrumb->title }}
                    </a>
                </li>
            @else
                <li class="active">
                    @if ($loop->first)
                        <i class="fa fa-home mr5"></i>
                    @endif
                    {{ $breadcrumb->title }}
                </li>
            @endif
        @endforeach
    </ol>
@endif
