<?php

namespace Terranet\Administrator\Dashboard;

use Traversable;

class DashboardRow implements \IteratorAggregate
{
    protected $panels = [];

    public function panel(DashboardPanel $panel)
    {
        $this->panels[] = $panel;

        return $panel;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->panels);
    }
}