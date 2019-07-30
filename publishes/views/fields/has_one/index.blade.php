<ul class="list-unstyled">
    @foreach($columns as $column)
        <li>
            <strong>{{ $column->title() }}: </strong> {!! $column->render(\Terranet\Administrator\Scaffolding::PAGE_INDEX) !!}
        </li>
    @endforeach
</ul>
