<?php

namespace Terranet\Administrator\Traits\Form;

trait SupportsListTypes
{
    /** @var string */
    public $type = 'select';

    /**
     * @return $this
     */
    public function datalist(): self
    {
        $this->type = 'datalist';

        return $this;
    }
}
