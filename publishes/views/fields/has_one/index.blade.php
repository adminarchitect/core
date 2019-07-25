<ul class="list-unstyled">
    @foreach($columns as $column)
        @if ($value = optional($related)->getAttribute($column->id()))
            <li>
                <strong>{{ $column->title() }}: </strong> {!! $column->render(\Terranet\Administrator\Scaffolding::PAGE_INDEX) !!}
            </li>
        @endif
    @endforeach
</ul>
