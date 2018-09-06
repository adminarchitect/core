<?php

namespace Terranet\Administrator\Field;

class Radio extends Generic
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return (array) $this->options;
    }

    /**
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function onIndex()
    {
        return [
            'options' => $this->getOptions(),
        ];
    }

    /**
     * @return array
     */
    public function onView()
    {
        return $this->onIndex();
    }

    /**
     * @return array
     */
    public function onEdit()
    {
        return $this->onIndex();
    }
}
