<?php

namespace Terranet\Administrator\Traits\Module;

use function admin\db\scheme;
use Closure;

trait HasSortable
{
    protected $sortable = null;

    public function sortable()
    {
        return $this->scaffoldSortable();
    }

    protected function scaffoldSortable()
    {
        if (!$this->model) {
            return [];
        }

        if (null === $this->sortable && ($schema = scheme())) {
            $this->sortable = (array) $this->excludeUnSortable(
                $schema->indexedColumns(
                    $this->model()->getTable()
                )
            );
        }

        return $this->sortable;
    }

    /**
     * @param $indexedColumns
     * @return array
     */
    protected function excludeUnSortable($indexedColumns)
    {
        if (property_exists($this, 'unSortable') && !empty($this->unSortable)) {
            $indexedColumns = array_diff($indexedColumns, $this->unSortable);
        }

        return $indexedColumns;
    }

    /**
     * Register a Sortable element.
     *
     * @param $element
     * @param Closure|null $callback
     * @return $this
     */
    public function addSortable($element, Closure $callback = null)
    {
        $this->scaffoldSortable();

        if (null === $callback) {
            $this->sortable[] = $element;
        } else {
            $this->sortable[$element] = $callback;
        }

        return $this;
    }

    /**
     * Remove an element from Sortable collection.
     *
     * @param $element
     * @return array|null
     */
    public function removeSortable($element)
    {
        if (array_has($this->sortable, $element)) {
            return $this->sortable = array_except($this->sortable, $element);
        }

        if (in_array($element, $this->sortable())) {
            return $this->sortable = array_except(
                $this->sortable,
                array_search($element, $this->sortable)
            );
        }

        return $this->sortable;
    }
}
