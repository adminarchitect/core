<?php

namespace Terranet\Administrator\Traits\Collection;

use Coduo\PHPHumanizer\StringHumanizer;
use Terranet\Administrator\Contracts\AutoTranslatable;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\AutoTranslatesInstances;

abstract class ElementContainer implements AutoTranslatable
{
    use AutoTranslatesInstances;

    /**
     * Element id.
     *
     * @var string
     */
    protected $id;

    /**
     * Element title.
     *
     * @var string
     */
    protected $title;

    /**
     * Relations chain.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Keep original id or extract only last part.
     *
     * @example: When using this Container in Forms, the original id should be kept to allow parsing relations.
     * When for columns there is a different logic to extract Relational data.
     * @var bool
     */
    protected $keepOriginalID = true;

    protected $module;

    /**
     * Element constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->setId($id);

        if ($this->translator()->has($key = $this->translationKey())) {
            $this->setTitle(trans($key));
        } else {
            $this->setTitle(
                'id' == $id ? 'ID' : StringHumanizer::humanize(str_replace(['_id'], '', $id))
            );
        }
    }

    /**
     * Set element ID.
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $id = $this->buildId($id);

        if ($this->isRelation($id)) {
            $relations = explode('.', $id);

            $lastElement = array_pop($relations);

            if (!$this->keepOriginalID) {
                $id = $lastElement;
            }

            $this->relations = $relations;
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Set element title.
     *
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        # handle relations-style titles
        $title = preg_replace('~^(\w+)\.(\w+)$~si', "$1 $2", $title);

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
     * @param $id
     * @return string
     */
    protected function buildId($id)
    {
        $parts = explode('.', $id);
        $parts = array_map(function ($part) {
            return str_slug($part, '_');
        }, $parts);
        $id = implode('.', $parts);

        return $id;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function isRelation($id)
    {
        return false !== stripos($id, '.');
    }

    /**
     * @return string
     */
    public function translationKey()
    {
        $key = sprintf('administrator::columns.%s.%s', $this->module()->url(), $this->id);

        if (!$this->translator()->has($key)) {
            $key = sprintf('administrator::columns.%s.%s', 'global', $this->id);
        }

        return $key;
    }

    /**
     * @return Scaffolding
     */
    protected function module()
    {
        return $this->module ?: app('scaffold.module');
    }

    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }
}
