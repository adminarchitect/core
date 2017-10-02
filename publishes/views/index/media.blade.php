<div id="media-{{ $id }}" class="carousel slide" data-ride="carousel" data-interval="{{ $interval }}" style="max-width: {{ $width }}px">
    <!-- Indicators -->
    @if (count($media) > 1 && $hasIndicators)
    <ol class="carousel-indicators">
        @foreach($media as $item)
            <li data-target="#media-{{ $id }}" data-slide-to="{{ $loop->index }}" class="{{ (0 === $loop->index ? 'active' : '') }}"></li>
        @endforeach
    </ol>
    @endif

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        @foreach($media as $item)
            <div class="item {{ (0 === $loop->index ? 'active' : '') }}">
                <img src="{{ $item->getUrl($conversion) }}" alt="">
            </div>
        @endforeach
    </div>

    @if(count($media) > 1 && $hasArrows)
        <a class="left carousel-control" href="#media-{{ $id }}" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <span class="sr-only">{{ trans('administrator::buttons.previous') }}</span>
        </a>
        <a class="right carousel-control" href="#media-{{ $id }}" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
            <span class="sr-only">{{ trans('administrator::buttons.next') }}</span>
        </a>
    @endunless
</div>
