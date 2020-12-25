<?php

namespace Terranet\Administrator\Field;

class Boolean extends Field
{
    /** @var mixed */
    public $trueValue = true;

    /** @var mixed */
    public $falseValue = false;

    /**
     * @param $value
     *
     * @return $this
     */
    public function trueValue($value): self
    {
        $this->trueValue = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function falseValue($value): self
    {
        $this->falseValue = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTrue(): bool
    {
        return $this->value() === $this->trueValue;
    }

    /**
     * @return array
     */
    public function onIndex(): array
    {
        return ['isTrue' => $this->isTrue()];
    }

    /**
     * @return array
     */
    public function onView(): array
    {
        return $this->onIndex();
    }
}
