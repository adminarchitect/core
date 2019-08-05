@php($page = \Terranet\Administrator\Scaffolding::PAGE_INDEX)

<ul class="list-unstyled">
    @foreach($columns->visibleOnPage($page) as $column)
        <li>
            <strong>{{ $column->title() }}: </strong> {!! $column->render($page) !!}
        </li>
    @endforeach
</ul>
