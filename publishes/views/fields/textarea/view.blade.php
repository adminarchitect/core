@if (!empty($value = $field->value()))
    @php($preview = \Illuminate\Support\Str::limit(strip_tags($value), 200))
    @php($full = strip_tags($value))
    <div class="preview">{!! nl2br($preview) !!}</div>
    @if (mb_strlen($full) !== mb_strlen($preview))
        <a href="#" onclick="toggleView(this); return false;">
            <strong>{!! trans('administrator::buttons.view_more') !!}</strong>
        </a>
        <div class="hidden">{!! nl2br($value) !!}</div>

        @push('scaffold.js')
            <script>
                function toggleView(object) {
                    [$(object), $(object).next(), $(object).prev()].forEach(e => {
                        e.toggleClass('hidden');
                    });
                    return false;
                }
            </script>
        @endpush
    @endif
@endif
