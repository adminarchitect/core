@component('administrator::components.table.group')
    @slot('title', $field->title())
    @slot('description', $field->getDescription())
@endcomponent

@foreach($columns as $field)
    @include('administrator::edit.row', ['field' => $field])
@endforeach
