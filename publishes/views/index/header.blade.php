<th style="white-space: nowrap; {{ ('id' == strtolower($column->id()) ? 'width: 70px;' : '') }};">
    @if ($resource->sortableManager()->canSortBy($column->id()))
        <a href="{{ $resource->sortableManager()->makeUrl($column->id()) }}">{{ $column->title() }}</a>
            {!! ($resource->sortableManager()->element() == $column->id()
            ? '<i style="margin-top: 3px;" class="fa '.('asc' == $resource->sortableManager()->direction() ? 'fa-sort-alpha-asc' : 'fa-sort-alpha-desc').'"></i>'
            : '') !!}
    @else
        {{ $column->title() }}
    @endif
</th>
