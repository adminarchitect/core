<?php

namespace Terranet\Administrator\Filter;

use Coduo\PHPHumanizer\StringHumanizer;
use Illuminate\Support\Facades\View;
use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

abstract class Filter implements Queryable
{
    use ExecutesQuery;

    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var mixed */
    protected $value;

    /**
     * Generic constructor.
     *
     * @param $title
     * @param null $id
     */
    private function __construct($title, $id = null)
    {
        $this->title = StringHumanizer::humanize($title);
        $this->id = snake_case($id ?: $this->title);
    }

    /**
     * @param $title
     * @param null $id
     * @param \Closure $callback
     *
     * @return static
     */
    public static function make($title, $id = null, \Closure $callback = null): self
    {
        $instance = new static($title, $id);

        if (null !== $callback) {
            $callback->call($instance, $instance);
        }

        return $instance;
    }

    /**
     * Return Element ID.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Form name.
     *
     * @return string
     */
    public function name()
    {
        $parts = explode('.', $this->id());

        if (\count($parts) > 1) {
            $first = array_first($parts);
            $other = \array_slice($parts, 1);

            $other = array_map(function ($part) {
                return "[$part]";
            }, $other);

            return implode('', array_merge([$first], $other));
        }

        return $this->id();
    }

    /**
     * Return Element title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Set Filter value.
     *
     * @param $value
     *
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return View::make($this->template(), [
                'field' => $this,
            ] + $this->renderWith());
    }

    /**
     * @return array
     */
    protected function renderWith()
    {
        return [];
    }

    /**
     * @param string $page
     * @param string $field
     *
     * @return string
     */
    protected function template(): string
    {
        return sprintf(
            'administrator::filters.%s',
            snake_case(class_basename($this))
        );
    }
}
