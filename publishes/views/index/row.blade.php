<tr>
    <th>
        <label for="collection_{{$item->id}}">
            <input type="checkbox"
                   name="collection[]"
                   id="collection_{{$item->getKey()}}"
                   value="{{ $item->getKey() }}"
                   class="collection-item simple"
            >
        </label>
    </th>
    @foreach($columns as $column)
        <td>
            @if(!$column->isGroup())
                {!! $column->render($item) !!}
            @else
                <ul class="list-unstyled">
                    @foreach($column->elements() as $element)
                        @if($value = $element->render($item))
                            <li>
                                @if ($element->standalone())
                                    <strong>{!! $value !!}</strong>
                                @else
                                    <label for="{{ $element->id() }}">{{ $element->title() }}:</label>
                                    {!! $value !!}
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </td>
    @endforeach

    @unless($actions->readonly())
        <td class="actions">
            <ul class="list-unstyled">
                @if ($actions->authorize('view', $item))
                    <li>
                        <a data-scaffold-action="{{ $module }}-view"
                           href="{{ route('scaffold.view', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
                            <i class="fa fa-eye"></i>
                            {{ trans('administrator::buttons.view') }}
                        </a>
                    </li>
                @endif
                @if ($actions->authorize('update', $item))
                    <li>
                        <a data-scaffold-action="{{ $module }}-edit"
                           href="{{ route('scaffold.edit', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
                            <i class="fa fa-pencil"></i>
                            {{ trans('administrator::buttons.edit') }}
                        </a>
                    </li>
                @endif
                @if ($actions->authorize('delete', $item))
                    <li>
                        <a data-scaffold-action="{{ $module }}-delete"
                           onclick="return confirm('{{ trans('administrator::messages.confirm_deletion') }}');"
                           href="{{ route('scaffold.delete', app('scaffold.magnet')->with(['module' => $module, 'id' => $item->getKey()])->toArray()) }}">
                            <i class="fa fa-trash"></i>
                            {{ trans('administrator::buttons.delete') }}
                        </a>
                    </li>
                @endif
                @foreach($actions->actions()->authorized(auth('admin')->user(), $item) as $action)
                    <li>
                        {!! $action->render($item) !!}
                    </li>
                @endforeach
            </ul>
        </td>
    @endunless
</tr>
