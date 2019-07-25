@foreach($columns as $column)
    @component('administrator::components.table.row')
        @slot('label', Form::label($column->id(), $column->title()))
        @slot('description', $column->getDescription())
        @slot('input', $column->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
    @endcomponent
@endforeach
