<?php

namespace Terranet\Administrator\Dashboard;

use Closure;
use IteratorAggregate;

class Manager implements IteratorAggregate
{
    protected $rows = [];

    /**
     * @param Closure $callback
     * @return static
     */
    public function row(Closure $callback)
    {
        $callback($row = new DashboardRow);

        $this->rows[] = $row;

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }
}