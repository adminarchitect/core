<?php

namespace Terranet\Administrator\Field\Traits;

trait HasEmptyValue
{
    protected $allowEmpty = false;

    /**
     * @param bool $flag
     * @return self
     */
    public function allowEmpty(bool $flag = false): self
    {
        $this->allowEmpty = (bool) $flag;

        return $this;
    }
}
