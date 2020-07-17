<?php

namespace Terranet\Administrator\Collection;

use Closure;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Field\Text;

class Mutable extends BaseCollection
{
    /**
     * Push an item onto the end of the collection.
     *
     * @param mixed $values [optional]
     * @return $this
     */
    public function push(...$values)
    {
        $element = $values[0] ?? null;
        $callback = $values[1] ?? null;

        $element = $this->createElement($element);

        if ($callback instanceof Closure) {
            $callback($element);
        }

        parent::push($element);

        return $this;
    }

    /**
     * Insert an element into collection at specified position.
     *
     * @param $element
     * @param $position
     * @param  null|Closure  $callback
     * @return $this
     * @throws Exception
     */
    public function insert($element, $position, Closure $callback = null): self
    {
        $element = $this->createElement($element);

        if ($callback) {
            $callback($element);
        }

        if (\is_string($position)) {
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
     * Get all items except for those with the specified keys.
     *
     * @param  array|mixed|string  $keys
     * @return static
     */
    public function except($keys)
    {
        if ($keys instanceof self) {
            $keys = $keys->all();
        } elseif (!\is_array($keys)) {
            $keys = \func_get_args();
        }

        $items = $this->filter(function ($element) use ($keys) {
            return !\in_array($element->id(), $keys, true);
        })->all();

        $this->items = array_values($items);

        return $this;
    }

    /**
     * Retrieve only visible items.
     *
     * @param  string  $page
     * @return Mutable
     */
    public function visibleOnPage(string $page)
    {
        return $this->filter(function ($item) use ($page) {
            // @var Generic|Translatable $item
            return (($item instanceof Group) || $item->isVisibleOnPage($page)) && $item->visibleWhen();
        });
    }

    /**
     * Update elements behaviour.
     *
     * @param  string  $id
     * @param  Closure  $callback
     * @return $this
     * @throws Exception
     */
    public function update(string $id, Closure $callback): self
    {
        if (Str::contains($id, ',')) {
            collect(explode(',', $id))
                ->map('trim')
                ->each(function ($element) use ($callback) {
                    $this->update($element, $callback);
                });

            return $this;
        }

        $element = $this->find($id);

        if ($element && $callback) {
            $newElement = $callback($element);
            if ($newElement !== $element) {
                $position = $this->position($id);
                $this->except($id);
                $this->insert($newElement, $position);
            }
        }

        return $this;
    }

    /**
     * Replace the field.
     *
     * @param  mixed  $id
     * @param $value
     * @return $this|BaseCollection
     * @throws Exception
     */
    public function switch($id, $value)
    {
        if ($position = $this->position($id)) {
            $this->offsetSet($position, $value);
        }

        return $this;
    }

    /**
     * Update many elements at once.
     *
     * @param  array  $ids
     * @return $this
     * @throws Exception
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
     * @param  int|mixed|string  $position
     * @return static
     * @throws Exception
     * @example: move('user_id', 4);
     * @example: move('user_id', 'before:name");
     * @example: move('user_id', 'after:id");
     */
    public function move(string $id, $position): self
    {
        if (is_numeric($position)) {
            return $this->toPosition($id, $position);
        }

        if (Str::startsWith($position, 'before:')) {
            return $this->moveBefore($id, substr($position, 7));
        }

        if (Str::startsWith($position, 'after:')) {
            return $this->moveAfter($id, substr($position, 6));
        }

        throw new Exception("Unknown moving direction: {$position}");
    }

    /**
     * Move element before another one.
     *
     * @param  string  $id
     * @param $target
     * @return $this
     * @throws Exception
     */
    public function moveBefore(string $id, $target)
    {
        if ($element = $this->find($id)) {
            $this->except($id);
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
     * @param  string  $id
     * @param $target
     * @return $this
     * @throws Exception
     */
    public function moveAfter(string $id, $target): self
    {
        if ($element = $this->find($id)) {
            $this->except($id);

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
     * @param  string  $id
     * @param  Closure  $callback
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
     * @param  array  $elements
     * @param  string  $groupId
     * @param  null|int|string  $position
     * @return $this
     * @throws Exception
     */
    public function stack(array $elements, string $groupId, $position = null): self
    {
        $group = new Group($groupId);

        $this->filter(function ($element) use ($elements) {
            return \in_array($element->id(), $elements, true);
        })->each(function ($element) use ($group) {
            $group->push($element);
            $this->items = $this->except($element->id())->all();
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
                    return $decorator->make($e);
                });

                return $element;
            }

            return $decorator->make($element);
        });
    }

    /**
     * Find an element.
     *
     * @param  string  $id
     * @return mixed
     * @throws Exception
     */
    public function find(string $id)
    {
        return $this->first(function ($element) use ($id) {
            return $element && $element->id() === $id;
        });
    }

    /**
     * Find an element position.
     *
     * @param  string  $id
     * @return null|int|string
     * @throws Exception
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
     * Make elements sortable.
     *
     * @param  mixed string|array $keys
     * @param  \Closure  $callback
     * @return Mutable
     * @example: sortable(['title' => function($query) {  }])
     * @example: sortable(['title'])
     */
    public function sortable($keys, \Closure $callback = null)
    {
        if (\is_array($keys)) {
            foreach ($keys as $id => $callback) {
                if (\is_string($id)) {
                    $this->sortable($id, $callback);
                } else {
                    $this->sortable($callback);
                }
            }

            return $this;
        }

        $module = app('scaffold.module');
        if ($module instanceof Sortable && method_exists($module, 'addSortable')) {
            $module->addSortable($keys, $callback);
        }

        return $this;
    }

    /**
     * Remove column from Sortable collection.
     *
     * @param  array|string  $keys
     * @return self
     */
    public function disableSorting($keys): self
    {
        if (!\is_array($keys)) {
            $keys = \func_get_args();
        }

        $module = app('scaffold.module');
        if ($module instanceof Sortable && method_exists($module, 'removeSortable')) {
            foreach ($keys as $key) {
                $module->removeSortable($key);
            }
        }

        return $this;
    }

    /**
     * Move an element to a position.
     *
     * @param  string  $id
     * @param  int|string  $position
     * @return static
     * @throws Exception
     */
    protected function toPosition(string $id, $position): self
    {
        $element = $this->find($id);

        return $this
            ->except($id)
            ->insert($element, $position);
    }

    /**
     * @param  string  $id
     * @throws Exception
     */
    protected function notFound(string $id)
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
        if (\is_string($element)) {
            $element = Text::make($element, $element);
        }

        return $element;
    }
}
