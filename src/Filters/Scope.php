<?php

namespace Terranet\Administrator\Filters;

use Illuminate\Support\Str;

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
     * @param null|string $id
     */
    public function __construct(string $title, string $id = null)
    {
        if (null === $id) {
            $id = $title;
        }
        $this->id = Str::snake($id);
        $this->title = Str::title($title);
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
     *
     * @return $this
     */
    public function setIcon($value)
    {
        if (Str::startsWith($value, 'fa-')) {
            $value = "fa {$value}";
        }

        if (Str::startsWith($value, 'glyphicon-')) {
            $value = "glyphicon {$value}";
        }

        $this->icon = $value;

        return $this;
    }
}
