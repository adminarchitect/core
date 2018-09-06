@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('description', $field->getDescription())
    @slot('input')
        <media-carousel
                id="{{ $field->id() }}"
                :has-indicators="true"
                :readonly="false"
                conversion="{{ $conversion or 'default' }}"
                :media="{{ $media }}"
                :width="320"
        ></media-carousel>
    @endslot
@endcomponent