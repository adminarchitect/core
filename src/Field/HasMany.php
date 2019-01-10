<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Field\Traits\WorksWithModules;
use Terranet\Administrator\Modules\Faked;

class HasMany extends Generic
{
    use WorksWithModules, HandlesRelation;

    /** @var string */
    protected $icon = 'list-ul';

    /** @var null|\Closure */
    protected $query;

    /**
     * @param \Closure $query
     *
     * @return $this
     */
    public function withQuery(\Closure $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $icon
     *
     * @return self
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Model $model
     * @param string $direction
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function sortBy(\Illuminate\Database\Eloquent\Builder $query, Model $model, string $direction): \Illuminate\Database\Eloquent\Builder
    {
        return $query->withCount($this->id())->orderBy("{$this->id()}_count", $direction);
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        $relation = $this->relation();
        $module = $this->firstWithModel($related = $relation->getRelated());

        // apply a query
        if ($this->query instanceof \Closure) {
            $relation = \call_user_func_array($this->query, [$relation]);
        }

        if ($module = $this->firstWithModel($related)) {
            $url = route('scaffold.view', [
                'module' => $module->url(),
                $related->getKeyName() => $related->getKey(),
                $relation->getForeignKeyName() => $this->model->getKey(),
            ]);
        }

        return [
            'icon' => $this->icon,
            'module' => $module,
            'count' => $relation->count(),
            'url' => $url ?? null,
        ];
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        $relation = $this->relation();
        $related = $relation->getRelated();

        // apply a query
        if ($this->query instanceof \Closure) {
            $relation = \call_user_func_array($this->query, [$relation]);
        }

        if (!$module = $this->relationModule()) {
            // Build a runtime module
            $module = Faked::make($related);
        }
        $columns = $module->columns()->each->disableSorting();
        $actions = $module->actionsManager();

        return [
            'module' => $module ?? null,
            'columns' => $columns ?? null,
            'actions' => $actions ?? null,
            'relation' => $relation ?? null,
            'items' => $relation ? $relation->getResults() : null,
        ];
    }
}
