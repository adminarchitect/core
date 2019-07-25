<?php

namespace Terranet\Administrator\Field;

class Boolean extends Field
{
    /** @var mixed */
    protected $trueValue = true;

    /** @var mixed */
    protected $falseValue = false;

    /**
     * @param $value
     *
     * @return $this
     */
    public function trueValue($value)
    {
        $this->trueValue = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function falseValue($value)
    {
        $this->falseValue = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->value() === $this->trueValue;
    }

    /**
     * @return array
     */
    public function onIndex()
    {
        return ['isTrue' => $this->isTrue()];
    }

    /**
     * @return array
     */
    public function onView()
    {
        return $this->onIndex();
    }
}
