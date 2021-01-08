<?php

namespace Terranet\Administrator\Traits\Form;

trait HasHtmlAttributes
{
    public $attributes = ['class' => 'form-control'];

    /**
     * @param array $attributes
     * @return static
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = array_replace($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param float $min
     * @return static
     */
    public function min(float $min)
    {
        $this->attributes['min'] = $min;

        return $this;
    }

    /**
     * @param null|float $max
     * @return static
     */
    public function max(float $max)
    {
        $this->attributes['max'] = $max;

        return $this;
    }

    /**
     * @param float $step
     * @return static
     */
    public function step(float $step)
    {
        $this->attributes['step'] = $step;

        return $this;
    }
}
