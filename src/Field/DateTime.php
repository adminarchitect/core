<?php

namespace Terranet\Administrator\Field;

use Carbon\Carbon;

class DateTime extends Generic
{
    /** @var string */
    protected $format = 'M j, Y g:i A';

    /**
     * @param string $page
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        return Carbon::parse($this->value())->format($this->format);
    }

    /**
     * @param string $format
     * @return self
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}