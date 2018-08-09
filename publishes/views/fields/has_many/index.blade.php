@if ($count)
    <span class="label label-success">
        @if ($url)
            <a href="{{ $url }}" style="color: white;">
                <i class="fa fa-{{ $icon }}"></i>&nbsp;{{ $count }}
            </a>
        @else
            <i class="fa fa-{{ $icon }}"></i>&nbsp;{{ $count }}
        @endif
    </span>
@endif