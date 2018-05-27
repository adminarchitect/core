<?php

namespace Terranet\Administrator\Form\Type;

use Terranet\Administrator\Form\Element;

class Key extends Element
{
    protected $value;

    public function render()
    {
        return $this->getValue();
    }
}
