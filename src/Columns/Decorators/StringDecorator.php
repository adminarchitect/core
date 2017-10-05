<?php

namespace Terranet\Administrator\Columns\Decorators;

class StringDecorator extends CellDecorator
{
    protected function render($row)
    {
        return \admin\helpers\eloquent_attribute($row, $this->name);
    }
}
