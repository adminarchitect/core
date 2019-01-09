<?php

namespace Terranet\Administrator\Filters;

use Terranet\Administrator\Traits\AutoTranslatesInstances;
use Terranet\Administrator\Traits\Collection\ElementContainer;

class Scope
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var callable */
    protected $query;

    /** @var null|string */
    protected $icon;

    /**
     * Scope constructor.
     *
     * @param string $title
     * @param string|null $id
     */
    public function __construct(string $title, string $id = null)
    {
        if (null === $id) {
            $id = $title;
        }
        $this->id = str_slug($id, '_');
        $this->title = $title;
    }

    /**
     * @param mixed $query
     *
     * @return $this
     */
    public function setQuery($query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setIcon($value)
    {
        if (starts_with($value, 'fa-')) {
            $value = "fa {$value}";
        }

        if (starts_with($value, 'glyphicon-')) {
            $value = "glyphicon {$value}";
        }

        $this->icon = $value;

        return $this;
    }

    /**
     * @return null|string
     */
    public function icon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}
