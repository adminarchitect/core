<ul class="list-unstyled">
    @foreach($columns as $column)
        @if ($value = optional($related)->getAttribute($column->id()))
            <li>
                <strong>{{ $column->title() }}: </strong> {{ $value }}
            </li>
        @endif
    @endforeach
</ul>