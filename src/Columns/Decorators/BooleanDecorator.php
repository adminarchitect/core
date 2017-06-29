<?php

namespace Terranet\Administrator\Columns\Decorators;

class BooleanDecorator extends CellDecorator
{
    public function getDecorator()
    {
        return function ($row) {
            return \admin\output\boolean($row->{$this->name});
        };
    }
}