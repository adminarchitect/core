<?php

namespace Terranet\Administrator\Contracts\Module;

interface Exportable
{
    /**
     * Available export formats.
     *
     * @return array
     */
    public function formats();

    /**
     * Get exportable url.
     *
     * @param $format
     *
     * @return string
     */
    public function makeExportableUrl($format);
}
