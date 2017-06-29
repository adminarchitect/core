@foreach($collection as $item)
    <li><!-- start notification -->
        <a href="{{ $item['url'] or '#' }}">
            <i class="{{ $item['icon'] }}"></i> {{ $item['message'] }}
        </a>
    </li><!-- end notification -->
@endforeach