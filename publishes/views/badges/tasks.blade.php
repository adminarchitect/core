@foreach($collection as $item)
    <li><!-- Task item -->
        <a href="{{ $item['url'] or '#' }}">
            <!-- Task title and progress text -->
            <h3>
                {{ $item['message'] }}
                <small class="pull-right">{{ $p = $item['progress'] }}%</small>
            </h3>
            <!-- The progress bar -->
            <div class="progress xs">
                <!-- Change the css width attribute to simulate progress -->
                <div class="progress-bar progress-bar-aqua" style="width: {{ $p }}%" role="progressbar"
                     aria-valuenow="{{ $p }}" aria-valuemin="0" aria-valuemax="100">
                    <span class="sr-only">{{ $p }}% Complete</span>
                </div>
            </div>
        </a>
    </li><!-- end task item -->
@endforeach