<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;

class Date extends Element
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'form-control',
        'style' => 'width: 262px;',
    ];

    /**
     * The specific rules for subclasses to override.
     *
     * @var array
     */
    protected $rules = [];

    public function render()
    {
        return '<!-- Scaffold: '.$this->getName().' -->'
            .'<div class="input-group">'
            .'    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>'
            .Form::text($this->getFormName(), $this->value, $this->attributes + ['data-filter-type' => $this->getClassName()])
            .'</div>';
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        $parts = explode('\\', get_class($this));

        return strtolower(array_pop($parts));
    }
}
