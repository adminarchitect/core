<?php

namespace Terranet\Administrator\Field;

class BelongsTo extends Generic
{
    /** @var string */
    protected $relation;

    /** @var string */
    protected $column = 'name';

    /** @var null\Closure */
    protected $format;

    /**
     * @param string $page
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        $method = $this->relation ?: $this->id;
        $hasRelation = method_exists($this->model, $method);

        $relation = $this->model->{$method};

        if (!$relation) {
            return null;
        }

        if ($this->format) {
            return call_user_func_array($this->format, [$relation, $this->model]);
        }

        return $this->linkToRelation($relation);
    }

    /**
     * @param string $relation
     * @return self
     */
    public function withName(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @param string $column
     * @return self
     */
    public function showAs(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param \Closure $format
     * @return BelongsTo
     */
    public function setCustomFormat(\Closure $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Build a link to related model.
     *
     * @param $relation
     * @return string
     */
    protected function linkToRelation($relation)
    {
        $module = app('scaffold.modules')->first(function ($module) use ($relation) {
            return get_class($module->model()) === get_class($relation);
        });

        if ($module) {
            return link_to_route('scaffold.view', $relation->{$this->column}, [
                'module' => $module->url(),
                $relation->getKeyName() => $relation->getKey(),
            ]);
        }

        return $relation->{$this->column};
    }
}