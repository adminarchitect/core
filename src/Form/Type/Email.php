<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Email extends Text
{
    public function render()
    {
        return Form::email($this->getFormName(), $this->value, $this->attributes);
    }
}
