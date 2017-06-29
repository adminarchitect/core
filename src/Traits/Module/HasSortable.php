<?php

namespace Terranet\Administrator\Traits\Module;

use function admin\db\scheme;

trait HasSortable
{
    public function sortable()
    {
        return $this->scaffoldSortable();
    }

    protected function scaffoldSortable()
    {
        if ($schema = scheme()) {
            $indexedColumns = $schema->indexedColumns($this->model()->getTable());

            return $this->excludeUnSortable($indexedColumns);
        }

        return [];
    }

    /**
     * @param $indexedColumns
     * @return array
     */
    protected function excludeUnSortable($indexedColumns)
    {
        if (property_exists($this, 'unSortable') && ! empty($this->unSortable)) {
            $indexedColumns = array_diff($indexedColumns, $this->unSortable);
        }

        return $indexedColumns;
    }
}
