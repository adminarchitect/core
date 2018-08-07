<?php

namespace Terranet\Administrator\Field;

class Boolean extends Generic
{
    /**
     * @param string $page
     *
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        return
            '<span style="color: '.($this->value() ? 'green' : 'inherit').'">'.
            '   <i class="fa fa-circle'.($this->value() ? '' : '-thin').'"></i>&nbsp;'.($this->value() ? 'True' : 'False').
            '</span>';
    }
}
