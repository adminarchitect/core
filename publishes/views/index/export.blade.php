@if (\admin\helpers\exportable($resource) && !empty($formats = $resource->formats()))
    <?php
    foreach ($formats as $format => $title) {
        if (is_numeric($format)) {
            $format = $title;
            $title = str_replace('_', ' ', $format);
        }
        $title = mb_strtoupper($title);
        $links[] = link_to($resource->makeExportableUrl($format), $title);
    }
    ?>
    <div class="export-collection" style="padding: 10px 0;">
        {{ trans('administrator::buttons.download') }}: {!! join(" | ", $links) !!}
    </div>
@endif
