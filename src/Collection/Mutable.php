<?php

namespace Terranet\Administrator\Collection;

use Closure;
use Illuminate\Support\Collection as BaseCollection;
use Terranet\Administrator\Columns\Element;
use Terranet\Administrator\Columns\MediaElement;
use Terranet\Administrator\Exception;

class Mutable extends BaseCollection
{
    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed $element
     * @param Closure|null $callback
     * @return $this
     */
    public function push($element, Closure $callback = null)
    {
        $element = $this->createElement($element);

        if ($callback) {
            $callback($element);
        }

        parent::push($element);

        return $this;
    }

    /**
     * @param string $collection
     *
     * @param Closure|null $callback
     * @param null $position
     * @return $this
     */
    public function media($collection = 'default', Closure $callback = null, $position = null)
    {
        $element = $this->createMediaElement($collection);

        if ($position) {
            $this->insert($element, $position, $callback);
        } else {
            $this->push($element, $callback);
        }

        return $this;
    }

    /**
     * Insert an element into collection at specified position.
     *
     * @param $element
     * @param $position
     * @param Closure|null $callback
     * @return $this
     */
    public function insert($element, $position, Closure $callback = null)
    {
        $element = $this->createElement($element);

        if ($callback) {
            $callback($element);
        }

        if (is_string($position)) {
            $this->push($element);

            return $this->move($element->id(), $position);
        }

        if ($position >= $this->count()) {
            return $this->push($element);
        }

        if ($position === 0) {
            return $this->prepend($element);
        }

        $items = [];
        foreach ($this->all() as $index => $value) {
            if ($index === $position) {
                array_push($items, $element);
            }

            array_push($items, $value);
        }
        $this->items = $items;

        return $this;
    }

    /**
     * Remove an element(s) from collection.
     *
     * @param int|array $id
     * @return static
     */
    public function without($id)
    {
        if (!is_array($id)) {
            $id = (array) $id;
        }

        $items = $this->filter(function ($element) use ($id) {
            return !in_array($element->id(), $id);
        })->all();

        $this->items = array_values($items);

        return $this;
    }

    public function standalone($columns)
    {
        foreach ($columns as $column) {
            $this->update($column, function ($e) {
                return $e->setStandalone(true);
            });
        }

        return $this;
    }

    /**
     * Update elements behaviour.
     *
     * @param $id
     * @param Closure $callback
     * @return $this
     */
    public function update($id, Closure $callback)
    {
        if (str_contains($id, ',')) {
            collect(explode(',', $id))
                ->map('trim')
                ->each(function ($element) use ($callback) {
                    $this->update($element, $callback);
                });

            return $this;
        }

        $element = $this->find($id);

        if ($element && $callback) {
            $callback($element);
        }

        return $this;
    }

    /**
     * Update many elements at once.
     *
     * @param $ids
     * @return $this
     */
    public function updateMany(array $ids = [])
    {
        foreach ($ids as $id => $callback) {
            $this->update($id, $callback);
        }

        return $this;
    }

    /**
     * Move element.
     *
     * @param $id
     * @param mixed|int|string $position
     * @return static
     *
     * @example: move('user_id', 4);
     * @example: move('user_id', 'before:name");
     * @example: move('user_id', 'after:id");
     * @throws Exception
     */
    public function move($id, $position)
    {
        if (is_numeric($position)) {
            return $this->toPosition($id, $position);
        }

        if (starts_with($position, 'before:')) {
            return $this->moveBefore($id, substr($position, 7));
        }

        if (starts_with($position, 'after:')) {
            return $this->moveAfter($id, substr($position, 6));
        }

        throw new Exception("Unknown moving direction: {$position}");
    }

    /**
     * Move an element to a position.
     *
     * @param $id
     * @param $position
     * @return static
     */
    protected function toPosition($id, $position)
    {
        $element = $this->find($id);

        return $this
            ->without($id)
            ->insert($element, $position);
    }

    /**
     * Move element before another one.
     *
     * @param $id
     * @param $target
     * @return $this
     */
    public function moveBefore($id, $target)
    {
        if ($element = $this->find($id)) {
            $this->without($id);
            $targetPosition = $this->position($target);

            if ($targetPosition >= 0) {
                $this->insert($element, $targetPosition)->all();
            }
        }

        return $this;
    }

    /**
     * Move element after another one.
     *
     * @param $id
     * @param $target
     * @return $this
     */
    public function moveAfter($id, $target)
    {
        if ($element = $this->find($id)) {
            $this->without($id);

            $targetPosition = $this->position($target);

            if ($targetPosition >= 0) {
                $this->insert($element, $targetPosition + 1)->all();
            }
        }

        return $this;
    }

    /**
     * Add a new elements group.
     *
     * @param $id
     * @param Closure $callback
     * @return $this
     */
    public function group($id, Closure $callback)
    {
        $group = new Group($id);

        $callback($group);

        $this->push($group);

        return $this;
    }

    /**
     * Join existing elements to a group.
     *
     * @param $elements
     * @param $groupId
     * @param null $position
     * @return $this
     */
    public function join($elements, $groupId, $position = null)
    {
        $group = new Group($groupId);

        $this->filter(function ($element) use ($elements) {
            return in_array($element->id(), $elements);
        })->each(function ($element) use ($group) {
            $group->push($element);
            $this->items = $this->without($element->id())->all();
        });

        if ($position) {
            $this->insert($group, $position);
        } else {
            $this->push($group);
        }

        return $this;
    }

    /**
     * Build a collection.
     *
     * @param $decorator
     * @return static
     */
    public function build($decorator)
    {
        return $this->map(function ($element) use ($decorator) {
            if ($element instanceof Group) {
                $element->map(function ($e) use ($decorator) {
                    return $decorator->makeElement($e);
                });

                return $element;
            }

            return $decorator->makeElement($element);
        });
    }

    /**
     * Find an element.
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $element = $this->first(function ($element) use ($id) {
            return $element->id() == $id;
        });

        if (!$element) {
            $this->notFound($id);
        };

        return $element;
    }

    /**
     * Find an element position.
     *
     * @param $id
     * @return int|null|string
     */
    public function position($id)
    {
        $i = 0;
        foreach ($this->all() as $item) {
            if ($item->id() == $id) {
                // stop immediately when victim found.
                return $i;
            }

            $i++;
        }

        $this->notFound($id);
    }

    protected function notFound($id)
    {
        throw new Exception(sprintf('Element [%s] does not exist.', $id));
    }

    /**
     * Create element object from string.
     *
     * @param $element
     * @return mixed
     */
    protected function createElement($element)
    {
        if (is_string($element)) {
            $element = new Element($element);
        }

        return $element;
    }

    /**
     * @param $collection
     * @return MediaElement
     */
    protected function createMediaElement($collection)
    {
        if (is_string($collection)) {
            $collection = new MediaElement($collection);
        }

        return $collection;
    }
}
