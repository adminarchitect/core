<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\View;
use Terranet\Administrator\Field\Traits\WorksWithModules;
use Terranet\Administrator\Scaffolding;

class HasMany extends Generic
{
    use WorksWithModules;

    /** @var string */
    protected $icon = 'list-ul';

    /** @var Builder */
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
     * @return \Illuminate\Contracts\View\View
     */
    protected function onIndex()
    {
        $relation = call_user_func([$this->model, $this->id]);
        $module = $this->firstWithModel($relation->getRelated());

        // apply a query
        if ($this->query) {
            $relation = call_user_func_array($this->query, [$relation]);
        }

        $count = $relation->count();

        $related = $relation->getRelated();

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
            'count' => $count,
            'url' => $url ?? null,
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    protected function onView()
    {
        $relation = call_user_func([$this->model, $this->id]);
        $module = $this->firstWithModel($relation->getRelated());

        if ($module) {
            $columns = $module->columns()->each->disableSorting();
            $actions = $module->actionsManager();
        }

        return [
            'module' => $module ?? null,
            'columns' => $columns ?? null,
            'actions' => $actions ?? null,
            'items' => $relation ? $relation->getResults() : null,
        ];
    }
}
