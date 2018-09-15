<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Support\Facades\View;

class Enum extends Filter
{
    /** @var array */
    protected $options;

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    protected function renderWith()
    {
        return [
            'options' => $this->options,
        ];
    }
}