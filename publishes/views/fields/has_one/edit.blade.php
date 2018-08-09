@component('administrator::components.table.group')
    @slot('title', $field->title())
@endcomponent
@foreach($columns as $field)
    {!! $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
@endforeach