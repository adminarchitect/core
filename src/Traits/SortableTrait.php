<?php

namespace Terranet\Administrator\Traits;

trait SortableTrait
{
    /**
     * @return mixed array|null
     */
    public function getSortable()
    {
        return property_exists($this, 'sortable') ? $this->sortable : null;
    }

    /**
     * Is Sortable item.
     *
     * @return bool
     */
    public function isSortable()
    {
        if (property_exists($this, 'sortable')) {
            return is_string($this->sortable) || is_callable($this->sortable);
        }

        return false;
    }
}
