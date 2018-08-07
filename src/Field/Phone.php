<?php

namespace Terranet\Administrator\Field;

class Phone extends Generic
{
    /**
     * @param string $page
     *
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        return $this->value()
            ? link_to("tel:{$this->value()}", $this->value())
            : null;
    }
}
