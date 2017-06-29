@foreach($collection as $item)
    <li><!-- start message -->
        <a href="{{ $item['url'] or '#' }}">
            <div class="pull-left">
                <!-- User Image -->
                <img src="{{ asset($item['image']) }}" class="img-circle" alt=""/>
            </div>
            <!-- Message title and timestamp -->
            <h4>
                {{ $item['title'] }}
                <small>
                    <i class="fa fa-clock-o"></i> {{ $item['time'] }}
                </small>
            </h4>
            <!-- The message -->
            <p>{{ $item['message'] }}</p>
        </a>
    </li><!-- end message -->
@endforeach