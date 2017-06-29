<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Password extends Text
{
    /**
     * The specific defaults for the image class.
     *
     * @var array
     */
    protected $passwordDefaults = [
        'setter' => true,
    ];

    /**
     * Gets all default values.
     *
     * @return array
     */
    public function getDefaults()
    {
        $defaults = parent::getDefaults();

        return array_merge($defaults, $this->passwordDefaults);
    }

    public function render()
    {
        return Form::password($this->getFormName(), $this->attributes);
    }
}
