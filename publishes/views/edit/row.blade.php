@if ($field instanceof \Terranet\Administrator\Collection\Group)
    @component('administrator::components.table.group')
        @slot('title', $field->title())
    @endcomponent
@else
    {!! $field->render(\Terranet\Administrator\Scaffolding::PAGE_EDIT) !!}
@endif