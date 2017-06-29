<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;

class Boolean extends Element
{
    protected $attributes = [];

    public function render()
    {
        return
            Form::hidden($this->getFormName(), 0, $this->hiddenAttributes()) .
            Form::checkbox($this->getFormName(), 1, $this->value, $this->attributes);
    }

    /**
     * @return array
     */
    protected function hiddenAttributes()
    {
        return ['id' => Form::getIdAttribute($this->getFormName(), $this->attributes) . '_hidden'];
    }
}
