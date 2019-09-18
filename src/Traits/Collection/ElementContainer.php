<?php

namespace Terranet\Administrator\Traits\Collection;

use Illuminate\Support\Str;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Contracts\AutoTranslatable;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\AutoTranslatesInstances;

abstract class ElementContainer implements AutoTranslatable
{
    use AutoTranslatesInstances;

    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var null|object */
    protected $module;

    /**
     * Element constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->setId($id);

        if (trans()->has($key = $this->translationKey())) {
            $this->setTitle(trans($key));
        } else {
            $this->setTitle(Architect::humanize($id));
        }
    }

    /**
     * Set element ID.
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $this->buildId($id);

        return $this;
    }

    /**
     * Set element title.
     *
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        // handle relations-style titles
        $title = preg_replace('~^(\w+)\.(\w+)$~si', '$1 $2', $title);

        $this->title = $title;

        return $this;
    }

    /**
     * Get element id.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get element title.
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function translationKey()
    {
        $key = sprintf('administrator::columns.%s.%s', $this->module()->url(), $this->id);

        if (!trans()->has($key)) {
            $key = sprintf('administrator::columns.%s.%s', 'global', $this->id);
        }

        return $key;
    }

    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function buildId($id)
    {
        $parts = explode('.', $id);
        $parts = array_map(function ($part) {
            return Str::slug($part, '_');
        }, $parts);

        return implode('.', $parts);
    }

    /**
     * @return Scaffolding
     */
    protected function module()
    {
        return $this->module ?: app('scaffold.module');
    }
}
