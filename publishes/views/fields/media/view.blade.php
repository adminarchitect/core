@php($endpoint = route('scaffold.fetch_media', [
    'module' => app('scaffold.module')->url(),
    'id' => app('scaffold.model')->id
]))

<media-library
        id="{{ $field->id() }}"
        collection="{{ ($collection ?? 'default') }}"
        endpoint="{{ $endpoint }}"
></media-library>