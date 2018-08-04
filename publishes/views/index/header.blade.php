@inject('columns', 'scaffold.columns')
@inject('sortable', 'scaffold.sortable')

<th style="white-space: nowrap; vertical-align: baseline; {{ ('id' == strtolower($column->id()) ? 'width: 70px;' : '') }};">
    @if ($sortable->canSortBy($column->id()))
        <a href="{{ $sortable->makeUrl($column->id()) }}">{{ $column->title() }}</a>
            {!! ($sortable->element() == $column->id()
            ? '<i style="margin-top: 3px;" class="pull-right fa '.('asc' == $sortable->direction() ? 'fa-sort-amount-asc' : 'fa-sort-amount-desc').'"></i>'
            : '') !!}
    @else
        {{ $column->title() }}
    @endif
</th>
