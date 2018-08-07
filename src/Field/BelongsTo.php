<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Field\Traits\AcceptsCustomFormat;
use Terranet\Administrator\Field\Traits\WorksWithModules;

class BelongsTo extends Generic
{
    use AcceptsCustomFormat,
        WorksWithModules;

    /** @var string */
    protected $relation;

    /** @var string */
    protected $column = 'name';

    /**
     * @param string $page
     *
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
            return $this->callFormatter($relation);
        }

        return $this->linkToRelation($relation);
    }

    /**
     * @param string $relation
     *
     * @return self
     */
    public function withName(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @param string $column
     *
     * @return self
     */
    public function showAs(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Build a link to related model.
     *
     * @param $relation
     *
     * @return string
     */
    protected function linkToRelation($relation)
    {
        if ($module = $this->firstWithModel($relation)) {
            return link_to_route('scaffold.view', $relation->{$this->column}, [
                'module' => $module->url(),
                $relation->getKeyName() => $relation->getKey(),
            ]);
        }

        return $relation->{$this->column};
    }
}
