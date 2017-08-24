<?php

namespace Terranet\Administrator\Traits\Module;

use Illuminate\Support\Facades\Request;

trait AllowFormats
{
    /**
     * Available export formats
     *
     * @return array
     */
    public function formats()
    {
        return $this->scaffoldFormats();
    }

    protected function scaffoldFormats()
    {
        return property_exists($this, 'exportableTo')
            ? $this->exportableTo
            : config('administrator.export.' . $this->url(), config('administrator.export.default'));
    }

    /**
     * Get exportable url
     *
     * @param $format
     * @return string
     */
    public function makeExportableUrl($format)
    {
        $payload = array_merge([
            'module' => $this->url(),
            'format' => $format,
        ], Request::all());

        return route('scaffold.export', $payload);
    }
}
