<?php

namespace Terranet\Administrator\Columns\Decorators;

class BooleanDecorator extends CellDecorator
{
    protected function render($row)
    {
        return \admin\output\boolean($row->{$this->name});
    }
}
