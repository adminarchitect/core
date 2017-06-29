@if ($breadcrumbs)
    <ol class="breadcrumb breadcrumb-quirk">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$breadcrumb->last)
                <li>
                    <a href="{{ $breadcrumb->url }}">
                        @if ($breadcrumb->first)
                            <i class="fa fa-home mr5"></i>
                        @endif
                        {{ $breadcrumb->title }}
                    </a>
                </li>
            @else
                <li class="active">
                    @if ($breadcrumb->first)
                        <i class="fa fa-home mr5"></i>
                    @endif
                    {{ $breadcrumb->title }}
                </li>
            @endif
        @endforeach
    </ol>
@endif
