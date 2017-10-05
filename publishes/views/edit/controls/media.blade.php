<media-carousel
    style="width: {{ $width }}px;"
    id="{{ $name }}"
    :has-arrows="{{ $arrows ? 'true' : 'false' }}"
    :has-indicators="{{ $indicators ? 'true' : 'false' }}"
    :readonly="{{ $editable ? 'false' : 'true' }}"
    conversion="{{ $conversion or 'default' }}"
    :media="{{ $media }}"
></media-carousel>