<?php

namespace Terranet\Administrator\Dashboard;

use Closure;
use IteratorAggregate;

class Manager implements IteratorAggregate
{
    /** @var array */
    protected $rows = [];

    /**
     * @param Closure $callback
     *
     * @return static
     */
    public function row(Closure $callback)
    {
        $callback($row = new Row());

        $this->rows[] = $row;

        return $this;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->rows);
    }
}
