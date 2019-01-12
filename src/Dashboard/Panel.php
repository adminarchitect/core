<?php

namespace Terranet\Administrator\Dashboard;

use Terranet\Administrator\Contracts\Services\Widgetable;

abstract class Panel implements Widgetable
{
    protected $width = 6;

    public function setWidth($width = 6)
    {
        $this->validateWidth($width);

        $this->width = (int) $width;

        return $this;
    }

    public function width()
    {
        return $this->width;
    }

    /**
     * @param $width
     *
     * @throws \Exception
     */
    protected function validateWidth($width)
    {
        if (!\in_array($width, range(1, 12, 1), true)) {
            throw new \Exception('Width must be between 1 and 12.');
        }
    }
}
