@php($page = \Terranet\Administrator\Scaffolding::PAGE_VIEW)

@foreach($columns->visibleOnPage($page) as $column)
    @if ($column instanceof \Terranet\Administrator\Field\Section)
        <tr>
            <td class="btn-quirk" colspan="2">{{ $column->title() }}</td>
        </tr>
    @endif
    @component('administrator::components.table.row')
        @slot('label', Form::label($column->id(), $column->title()))
        @slot('description', $column->getDescription())
        @slot('input', $column->render($page))
    @endcomponent
@endforeach
