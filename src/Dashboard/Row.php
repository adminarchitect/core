<?php

namespace Terranet\Administrator\Dashboard;

use Traversable;

class Row implements \IteratorAggregate
{
    protected $panels = [];

    public function panel(Panel $panel)
    {
        $this->panels[] = $panel;

        return $panel;
    }

    /**
     * Retrieve an external iterator.
     *
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     *
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->panels);
    }
}
