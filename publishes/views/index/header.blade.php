@inject('sortable', 'scaffold.sortable')

<th style="white-space: nowrap; {{ ('id' == strtolower($column->id()) ? 'width: 70px;' : '') }};">
    @if ($sortable->canSortBy($column->id()))
        <a href="{{ $sortable->makeUrl($column->id()) }}">{{ $column->title() }}</a>
            {!! ($sortable->element() == $column->id()
            ? '<i style="margin-top: 3px;" class="fa '.('asc' == $sortable->direction() ? 'fa-sort-alpha-asc' : 'fa-sort-alpha-desc').'"></i>'
            : '') !!}
    @else
        {{ $column->title() }}
    @endif
</th>
