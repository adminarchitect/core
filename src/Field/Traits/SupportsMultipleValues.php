<?php

namespace Terranet\Administrator\Field\Traits;

trait SupportsMultipleValues
{
    public $isArray = false;

    public function isArray(): self
    {
        $this->isArray = true;

        return $this;
    }
}
