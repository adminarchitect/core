<?php

namespace Terranet\Administrator\Field;

class Email extends Generic
{
    /**
     * @param string $page
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        return $this->value()
            ? link_to('mailto:'.$this->value(), $this->value())
            : null;
    }
}