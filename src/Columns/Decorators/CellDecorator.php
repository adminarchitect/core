<?php

namespace Terranet\Administrator\Columns\Decorators;

abstract class CellDecorator
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}