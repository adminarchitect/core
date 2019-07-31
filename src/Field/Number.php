<?php

namespace Terranet\Administrator\Field;

class Number extends Field
{
    /** @var min|float */
    protected $min = 0;

    /** @var null|float */
    protected $max = null;

    /** @var null|double */
    protected $step = 1;

    /**
     * @param  float  $min
     * @return $this
     */
    public function min(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param  null|float  $max
     * @return $this
     */
    public function max(?float $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param  float  $step
     * @return $this
     */
    public function step(float $step): self
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return parent::getAttributes() + [
                'min' => $this->min,
                'max' => $this->max,
                'step' => $this->step,
                'style' => 'width: 150px',
            ];
    }
}
