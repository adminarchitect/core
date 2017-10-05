<?php

namespace Terranet\Administrator\Columns\Decorators;

class RankDecorator extends CellDecorator
{
    protected function render($row)
    {
        return \admin\output\rank($this->name, $row->{$this->name}, $row->getKey());
    }
}