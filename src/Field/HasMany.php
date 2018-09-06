<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\View;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Field\Traits\WorksWithModules;
use Terranet\Administrator\Modules\Faked;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Services\CrudActions;
use Terranet\Administrator\Traits\Module\HasColumns;

class HasMany extends Generic
{
    use WorksWithModules, HandlesRelation;

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
     * @return array
     */
    protected function onIndex(): array
    {
        $relation = $this->relation();
        $module = $this->firstWithModel($related = $relation->getRelated());

        // apply a query
        if ($this->query) {
            $relation = call_user_func_array($this->query, [$relation]);
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

    /**
     * @return array
     */
    public function onEdit(): array
    {
        $relation = $this->relation();

        if (static::MODE_CHECKBOXES === $this->editMode && $this->completeList) {
            $values = $relation->getRelated()->all();
        } else {
            $values = $this->value();
        }

        return [
            'relation' => $relation,
            'searchable' => get_class($relation->getRelated()),
            'values' => $values,
            'completeList' => $this->completeList,
            'titleField' => $this->titleField,
            'editMode' => $this->editMode,
        ];
    }
}
