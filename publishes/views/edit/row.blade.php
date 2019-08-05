@if ($field instanceof \Terranet\Administrator\Field\Media)
    <!-- Skip media on edit -->
@elseif ($field instanceof \Terranet\Administrator\Field\Hidden)
    <!-- Skip hidden input -->
@elseif ($field instanceof \Terranet\Administrator\Collection\Group)
    @component('administrator::components.table.group')
        @slot('title', $field->title())
        @slot('elements')
            @each('administrator::edit.row', $field->elements(), 'field')
        @endslot
    @endcomponent
@elseif ($field instanceof \Terranet\Administrator\Field\Section)
    <tr>
        <th class="btn-quirk" colspan="2">{{ $field->title() }}</th>
    </tr>
@else
    @component('administrator::components.table.row', [
        'section' => $field instanceof \Terranet\Administrator\Field\HasOne || $field instanceof \Terranet\Administrator\Field\BelongsToMany
    ])
        @slot('label', Form::label($field->id(), $field->title()))
        @slot('description', $field->getDescription())
        @slot('input', $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT))
    @endcomponent
@endif
