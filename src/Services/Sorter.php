<?php

namespace Terranet\Administrator\Services;

class Sorter
{
    protected $sortable;

    protected $element;

    protected $direction;

    public function __construct(array $sortable = [], $sort_dir = 'desc')
    {
        $this->sortable = $sortable;

        $this->element = $this->input('sort_by', $this->first());

        $this->direction = $this->input('sort_dir', $sort_dir);
    }

    /**
     * Retrieve first sortable element
     *
     * @return mixed
     */
    protected function first()
    {
        foreach ($this->sortable as $key => $value) {
            if (is_numeric($key)) {
                return $value;
            }

            return $key;
        }

        return null;
    }

    /**
     * Build sortable url
     *
     * @param $element
     * @return string
     */
    public function makeUrl($element)
    {
        return \admin\helpers\qsRoute(null, [
            'sort_by' => $element,
            'sort_dir' => $this->proposeDirection($element),
        ]);
    }

    /**
     * Propose new sort direction for element
     *
     * @param $forElement
     * @return string
     */
    protected function proposeDirection($forElement)
    {
        $sortDir = $this->direction();

        return $forElement == $this->element() ? $this->reverseDirection($sortDir) : $sortDir;
    }

    /**
     * Get current sorting direction
     *
     * @return mixed
     */
    public function direction()
    {
        return $this->direction;
    }

    /**
     * Get current sorting element
     *
     * @return mixed
     */
    public function element()
    {
        return $this->element ?: $this->first();
    }

    /**
     * Reverse sorting direction
     *
     * @param $direction
     * @return string
     */
    protected function reverseDirection($direction)
    {
        return 'asc' == strtolower($direction) ? 'desc' : 'asc';
    }

    /**
     * Check if a column is sortable
     *
     * @param $column
     * @return bool
     */
    public function canSortBy($column)
    {
        return array_key_exists($column, $this->sortable) || in_array($column, $this->sortable);
    }

    protected function input($key, $default = null)
    {
        return app('request')->input($key, $default);
    }
}
