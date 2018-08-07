<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Field\Traits\AcceptsCustomFormat;
use Terranet\Administrator\Field\Traits\WorksWithModules;

class HasMany extends Generic
{
    use AcceptsCustomFormat,
        WorksWithModules;

    /** @var string */
    protected $icon = 'list-ul';

    /**
     * @param string $page
     *
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        $relation = call_user_func([$this->model, $this->id]);

        if (!$relation) {
            return null;
        }

        if ($this->format) {
            return $this->callFormatter($relation);
        }

        if ($count = $relation->count()) {
            return '<span class="label label-success">'.$this->linkToRelation($relation, $count).'</span>';
        }

        return null;
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
     * Build a link to related model.
     *
     * @param mixed $relation
     * @param mixed $count
     *
     * @return string
     */
    protected function linkToRelation($relation, $count)
    {
        $related = $relation->getRelated();

        if ($module = $this->firstWithModel($related)) {
            $url = route('scaffold.view', [
                'module' => $module->url(),
                $related->getKeyName() => $related->getKey(),
                $relation->getForeignKeyName() => $this->model->getKey(),
            ]);

            return link_to($url, $this->renderIcon().'&nbsp;'.$count, [
                'style' => 'color: white;',
            ], false, false);
        }

        return $this->renderIcon().'&nbsp;'.$count;
    }

    /**
     * @return string
     */
    protected function renderIcon()
    {
        return '<i class="fa fa-'.ltrim($this->icon, 'fa-').'"></i>';
    }
}
