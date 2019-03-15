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
     * @param null|Closure $callback
     *
     * @return $this
     */
    public function push($element, Closure $callback = null): self
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
     * @param null|Closure $callback
     * @param null $position
     *
     * @return $this
     */
    public function media(string $collection = 'default', Closure $callback = null, $position = null): self
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
     * @param null|Closure $callback
     *
     * @return $this
     */
    public function insert($element, $position, Closure $callback = null): self
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

        if (0 === $position) {
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
     * @param array|int $id
     *
     * @return self
     */
    public function without($id): self
    {
        if (!is_array($id)) {
            $id = (array) $id;
        }

        $items = $this->filter(function ($element) use ($id) {
            return !in_array($element->id(), $id, true);
        })->all();

        $this->items = array_values($items);

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function standalone(array $columns = []): self
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
     * @param string $id
     * @param Closure $callback
     *
     * @return $this
     */
    public function update(string $id, Closure $callback): self
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
     *
     * @return $this
     */
    public function updateMany(array $ids = []): self
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
     * @param int|mixed|string $position
     *
     * @throws Exception
     *
     * @return static
     *
     * @example: move('user_id', 4);
     * @example: move('user_id', 'before:name");
     * @example: move('user_id', 'after:id");
     */
    public function move(string $id, $position): self
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
     * Move element before another one.
     *
     * @param string $id
     * @param $target
     *
     * @return $this
     */
    public function moveBefore(string $id, $target)
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
     * @param string $id
     * @param $target
     *
     * @return $this
     */
    public function moveAfter(string $id, $target): self
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
     * @param string $id
     * @param Closure $callback
     *
     * @return $this
     */
    public function group(string $id, Closure $callback): self
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
     *
     * @return $this
     */
    public function stack($elements, $groupId, $position = null): self
    {
        $group = new Group($groupId);

        $this->filter(function ($element) use ($elements) {
            return in_array($element->id(), $elements, true);
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
     *
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
     * @param string $id
     *
     * @return mixed
     */
    public function find(string $id)
    {
        $element = $this->first(function ($element) use ($id) {
            return $element->id() === $id;
        });

        if (!$element) {
            $this->notFound($id);
        }

        return $element;
    }

    /**
     * Find an element position.
     *
     * @param string $id
     *
     * @return null|int|string
     */
    public function position(string $id): int
    {
        $i = 0;
        foreach ($this->all() as $item) {
            if ($item->id() === $id) {
                return $i;
            }

            ++$i;
        }

        return $this->notFound($id);
    }

    /**
     * Move an element to a position.
     *
     * @param string $id
     * @param $position
     *
     * @return static
     */
    protected function toPosition(string $id, $position): self
    {
        $element = $this->find($id);

        return $this
            ->without($id)
            ->insert($element, $position);
    }

    /**
     * @param $id
     * @throws Exception
     */
    protected function notFound($id)
    {
        throw new Exception(sprintf('Element [%s] does not exist.', $id));
    }

    /**
     * Create element object from string.
     *
     * @param $element
     *
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
     *
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
