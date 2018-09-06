@if ($module)
    @component('administrator::components.index.table')
        @slot('headers')
            @each('administrator::index.header', $columns->visibleOnPage('index'), 'column')
            @unless($actions->readonly())
                <th class="actions" style="width: 100px; min-width: 100px;"></th>
            @endunless
        @endslot

        @slot('rows')
            @forelse($items as $item)
                <tr>
                    @foreach($columns = $columns->visibleOnPage('index') as $column)
                        <td>
                            @include('administrator::index.cell', ['column' => $column, 'item' => $item])
                        </td>
                    @endforeach
                    @unless($actions->readonly())
                        <td class="actions">
                            @include('administrator::index.row_actions', ['actions' => $actions, 'module' => $module])
                        </td>
                    @endunless
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $columns->count() }}">No data</td>
                </tr>
            @endforelse
        @endslot
    @endcomponent
@endif