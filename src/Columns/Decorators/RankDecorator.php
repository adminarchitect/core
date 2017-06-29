<?php

namespace Terranet\Administrator\Columns\Decorators;

class RankDecorator extends CellDecorator
{
    public function getDecorator()
    {
        return function ($row) {
            return \admin\output\rank($this->name, $row->{$this->name}, $row->getKey());
        };
    }
}