<script>
    window.mediaFiles = {!! json_encode($files) !!};
    window.UPLOADER_URL = '{{ route('scaffold.media.upload') }}';
    window.REQUEST_PATH = '{{ request('path', '') }}';
    window.mediaPopup = {{ ($popup ? 'true' : 'false') }};
</script>