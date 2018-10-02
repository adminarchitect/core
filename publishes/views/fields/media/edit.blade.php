<media-carousel
        id="{{ $field->id() }}"
        :has-indicators="true"
        :readonly="false"
        conversion="{{ $conversion or 'default' }}"
        :media="{{ $media }}"
        :width="320"
></media-carousel>
