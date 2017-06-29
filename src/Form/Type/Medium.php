<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Medium extends Textarea
{
    public function render()
    {
        $attributes = $this->attributes + ['data-editor' => 'medium'];

        return Form::textarea($this->getFormName(), $this->value, $attributes);
    }
}
