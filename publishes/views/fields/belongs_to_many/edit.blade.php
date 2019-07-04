<tr>
    <td colspan="2">
        @if (\Terranet\Administrator\Field\BelongsToMany::MODE_TAGS === $editMode)
            <tag-list :items="{{ $field->value() }}"
                      name="{{ $field->name() }}"
                      key-name="{{ $relation->getRelated()->getKeyName() }}"
                      label-name="{{ $titleField }}"
                      search-url="{{ route('scaffold.search', ['searchable' => $searchable, 'field' => $titleField]) }}"
            ></tag-list>
        @elseif(\Terranet\Administrator\Field\BelongsToMany::MODE_CHECKBOXES === $editMode)
            <ul class="list-unstyled">
                @foreach($values as $related)
                    <li style="width: 200px; display: inline-block">
                        <label>
                            <input type="checkbox"
                                   name="{{ $field->name() }}[]"
                                   value="{{ $related->getKey() }}" {!! $field->value()->contains($related) ? 'checked="checked"': '' !!}>
                            {{ $related->{$titleField} }}
                        </label>
                    </li>
                @endforeach
            </ul>
        @endif
    </td>
</tr>
