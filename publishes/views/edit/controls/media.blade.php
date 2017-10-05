<media-carousel
    id="{{ $name }}"
    style="width: {{ $width }}px"
    :has-arrows="{{ $arrows ? 'true' : 'false' }}"
    :has-indicators="{{ $indicators ? 'true' : 'false' }}"
    :readonly="{{ $editable ? 'false' : 'true' }}"
    conversion="{{ $conversion or 'default' }}"
    :media="{{ $media }}"
></media-carousel>