<tr>
    @if ($actions->batch()->count() && !$actions->readonly())
        <th>
            <label for="collection_{{$item->getKey()}}">
                <input type="checkbox"
                       name="collection[]"
                       id="collection_{{$item->getKey()}}"
                       value="{{ $item->getKey() }}"
                       class="collection-item simple"
                >
            </label>
        </th>
    @endif
    @foreach($columns->visibleOnPage('index') as $column)
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
