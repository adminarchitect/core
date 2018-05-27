<?php

namespace Terranet\Administrator\Badges;

use Illuminate\Support\Collection as BaseCollection;

class Collection
{
    /**
     * Collection items.
     *
     * @var BaseCollection
     */
    protected $items;

    /**
     * @var \Closure
     */
    protected $transformer;

    /**
     * Collection constructor.
     *
     * @param mixed array|BaseCollection $items
     * @param \Closure $transformer
     */
    public function __construct($items, \Closure $transformer)
    {
        $this->items = collect($items);

        $this->transformer = $transformer;
    }

    /**
     * Transform items according to $transformer closure.
     *
     * @return static
     */
    public function items()
    {
        return $this->items->map($this->transformer);
    }
}
