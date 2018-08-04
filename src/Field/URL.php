<?php

namespace Terranet\Administrator\Field;

class URL extends Generic
{
    /**
     * @param string $page
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        return $this->value()
            ? link_to($this->value(), $this->value(), ['target' => '_blank'])
            : null;
    }
}