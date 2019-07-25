<?php

namespace Terranet\Administrator\Field;

class Number extends Field
{
    /** @var int */
    protected $min = 0;

    /** @var null|int */
    protected $max = null;

    /** @var int|double */
    protected $step = 1;

    /**
     * @param  int  $min
     * @return $this
     */
    public function min(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @param  null|int  $max
     * @return $this
     */
    public function max(?int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param  float|int  $step
     * @return $this
     */
    public function step($step): self
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
