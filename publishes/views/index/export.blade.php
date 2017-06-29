@if (\admin\helpers\exportable($module) && !empty($formats = $module->formats()))
    <?php
    foreach ($formats as $format => $title) {
        if (is_numeric($format)) {
            $format = $title;
            $title = str_replace('_', ' ', $format);
        }
        $title = mb_strtoupper($title);
        $links[] = link_to($module->makeExportableUrl($format), $title);
    }
    ?>
    <div class="export-collection" style="padding: 10px 0;">
        {{ trans('administrator::buttons.download') }}: {!! join(" | ", $links) !!}
    </div>
@endif
