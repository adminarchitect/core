<?php

namespace Terranet\Administrator\Collection;

use Terranet\Administrator\Collection\Mutable as MutableCollection;
use Terranet\Administrator\Columns\Element;
use Terranet\Administrator\Traits\Collection\ElementContainer;

class Group extends ElementContainer
{
    /**
     * @var MutableCollection
     */
    protected $elements = [];

    /**
     * Group constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        parent::__construct($id);

        $this->elements = new MutableCollection([]);
    }

    /**
     * Push an.
     *
     * @param $element
     *
     * @return $this
     */
    public function push(Element $element)
    {
        $this->elements->push($element);

        return $this;
    }

    public function merge($elements = [])
    {
        $this->elements = $this->elements->merge($elements);

        return $this;
    }

    /**
     * Insert an element into collection at specified position.
     *
     * @param $element
     * @param $position
     *
     * @return $this
     */
    public function insert(Element $element, $position)
    {
        $this->elements = $this->elements->insert($element, $position);

        return $this;
    }

    /**
     * Remove an element from collection.
     *
     * @param $id
     *
     * @return static
     */
    public function without($id)
    {
        $this->elements = $this->elements->without($id);

        return $this;
    }

    /**
     * Update elements behaviour.
     *
     * @param $id
     * @param \Closure $callback
     *
     * @return $this
     */
    public function update($id, \Closure $callback)
    {
        $this->elements = $this->elements->update($id, $callback);

        return $this;
    }

    /**
     * Update many elements at once.
     *
     * @param $ids
     *
     * @return $this
     */
    public function updateMany(array $ids = [])
    {
        $this->elements = $this->elements->updateMany($ids);

        return $this;
    }

    /**
     * Move an element to a position.
     *
     * @param $id
     * @param $position
     *
     * @return $this
     */
    public function move($id, $position)
    {
        $this->elements = $this->elements->move($id, $position);

        return $this;
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     *
     * @return static
     */
    public function map(callable $callback)
    {
        $this->elements = $this->elements->map($callback);

        return $this;
    }

    /**
     * Find element by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->elements->find($id);
    }

    public function elements()
    {
        return $this->elements;
    }

    public function isGroup()
    {
        return true;
    }
}
