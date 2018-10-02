@if ($media->count())
    <media-carousel
            id="{{ $field->id() }}"
            :has-indicators="true"
            :readonly="true"
            conversion="{{ $conversion or 'default' }}"
            :media="{{ $media->toJson() }}"
    ></media-carousel>
@endif