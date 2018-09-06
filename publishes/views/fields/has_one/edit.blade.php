@component('administrator::components.table.group')
    @slot('title', $field->title())
    @slot('description', $field->getDescription())
@endcomponent
@foreach($columns as $field)
    {!! $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
@endforeach