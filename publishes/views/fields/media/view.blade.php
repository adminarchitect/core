@if ($media->count())
    <media-carousel
            id="{{ $field->id() }}"
            :has-indicators="true"
            :readonly="true"
            conversion="{{ $conversion ?? 'default' }}"
            :media="{{ $media->toJson() }}"
    ></media-carousel>
@endif