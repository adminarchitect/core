<?php

namespace Terranet\Administrator\Form\Type;

use Terranet\Administrator\Form\Element;

class Time extends Element
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
        $input = \Form::input('time', $this->getFormName(), $this->value, $this->attributes);

        return <<<OUT
<div class="input-group timepicker">
	<div class="input-group-addon">
		<i class="fa fa-calendar"></i>
	</div>
	{$input}
</div>

OUT;
    }
}
