<?php

namespace Terranet\Administrator\Field;

class Radio extends Field
{
    /** @var array */
    public $options = [];

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
}
