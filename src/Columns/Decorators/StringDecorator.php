<?php

namespace Terranet\Administrator\Columns\Decorators;

class StringDecorator extends CellDecorator
{
    public function getDecorator()
    {
        return function ($row) {
            return \admin\helpers\eloquent_attribute($row, $this->name);
        };
    }
}
