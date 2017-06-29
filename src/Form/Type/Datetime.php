<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;

class Datetime extends Element
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
        .Form::input('datetime', $this->getFormName(), $this->value, $this->attributes + ['data-filter-type' => 'date'])
        .'</div>';
    }
}
