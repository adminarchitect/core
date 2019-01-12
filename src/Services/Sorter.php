<?php

namespace Terranet\Administrator\Services;

class Sorter
{
    /** @var array */
    protected $sortable;

    /** @var null|string */
    protected $element;

    /** @var null|string */
    protected $direction;

    /**
     * Sorter constructor.
     *
     * @param array $sortable
     * @param string $sortDir
     */
    public function __construct(array $sortable = [], string $sortDir = 'desc')
    {
        $this->sortable = $sortable;

        $this->element = $this->input('sort_by', $this->first());

        $this->direction = $this->input('sort_dir', $sortDir);
    }

    /**
     * Build sortable url.
     *
     * @param string $element
     *
     * @return string
     */
    public function makeUrl(string $element)
    {
        return \admin\helpers\qsRoute(null, [
            'sort_by' => $element,
            'sort_dir' => $this->proposeDirection($element),
        ]);
    }

    /**
     * Get current sorting direction.
     *
     * @return null|string
     */
    public function direction(): ?string
    {
        return $this->direction;
    }

    /**
     * Get current sorting element.
     *
     * @return null|string
     */
    public function element(): ?string
    {
        return $this->element ?: $this->first();
    }

    /**
     * Check if a column is sortable.
     *
     * @param string $column
     *
     * @return bool
     */
    public function canSortBy(string $column): bool
    {
        return array_key_exists($column, $this->sortable) || \in_array($column, $this->sortable, true);
    }

    /**
     * Retrieve first sortable element.
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
     * Propose new sort direction for element.
     *
     * @param $forElement
     *
     * @return string
     */
    protected function proposeDirection($forElement)
    {
        $sortDir = $this->direction();

        return $forElement === $this->element() ? $this->reverseDirection($sortDir) : $sortDir;
    }

    /**
     * Reverse sorting direction.
     *
     * @param string $direction
     *
     * @return string
     */
    protected function reverseDirection(string $direction)
    {
        return 'asc' === strtolower($direction) ? 'desc' : 'asc';
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    protected function input($key, $default = null)
    {
        return app('request')->input($key, $default);
    }
}
