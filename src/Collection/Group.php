<?php

namespace Terranet\Administrator\Collection;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Collection\Mutable as MutableCollection;
use Terranet\Administrator\Traits\Collection\ElementContainer;

/**
 * Class Group.
 *
 * @method merge(array $elements)
 * @method insert($element, $position)
 * @method withoug(string $id)
 * @method update(string $id, \Closure $callback)
 * @method updateMany(array $ids)
 * @method move(string $id, $position)
 * @method map(callable $callback)
 */
class Group extends ElementContainer
{
    /**
     * @var MutableCollection
     */
    protected $elements;

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
     * @param $method
     * @param $args
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (method_exists($this->elements, $method)) {
            $this->elements = \call_user_func_array([$this->elements, $method], $args);

            return $this;
        }

        throw new \Exception(sprintf('Unknwon method "%s"', $method));
    }

    /**
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->elements->each->setModel($model);

        return $this;
    }

    /**
     * Push an.
     *
     * @param $element
     *
     * @return $this
     */
    public function push($element)
    {
        $this->elements->push($element);

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

    /**
     * @return Mutable
     */
    public function elements()
    {
        return $this->elements;
    }
}
