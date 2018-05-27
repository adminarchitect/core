<?php

namespace Terranet\Administrator\Traits\Module;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Collection\Group;
use Terranet\Administrator\Collection\Mutable as MutableCollection;
use Terranet\Administrator\Decorators\Grid as GridDecorator;
use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Translatable\Translatable;

trait HasColumns
{
    /**
     * Fetch scaffold columns.
     *
     * @return MutableCollection
     */
    public function columns()
    {
        return $this->scaffoldColumns();
    }

    /**
     * Define the list of attributes visible on View model page.
     *
     * @param Model $model
     *
     * @return Mutable
     */
    public function viewColumns(Model $model = null)
    {
        return $this->scaffoldViewColumns($model ?: app('scaffold.model'));
    }

    /**
     * Fetch "viewable" columns.
     *
     * @param Model $model
     *
     * @return Mutable
     */
    public function scaffoldViewColumns(Model $model)
    {
        $items = new Mutable();

        $attributes = \admin\helpers\eloquent_attributes($model);

        foreach ($attributes as $key => $value) {
            $items->push($key);
        }

        return $items->build(
            new GridDecorator($model)
        );
    }

    /**
     * Scaffold columns.
     *
     * @return MutableCollection
     */
    protected function scaffoldColumns()
    {
        return $this->collectColumns(
            $this->model()
        );
    }

    /**
     * @param $model
     *
     * @return MutableCollection
     */
    protected function collectColumns(Model $model = null)
    {
        if (!$model) {
            return new MutableCollection([]);
        }

        $pk = $model->getKeyName();

        $fillable = array_merge(
            is_array($pk) ? $pk : [$pk],
            $model->getFillable()
        );
        $hidden = $model->getHidden();

        if ($model instanceof Translatable && method_exists($model, 'getTranslatedAttributes')) {
            $fillable = array_merge($fillable, $model->getTranslatedAttributes());
            $hidden = array_merge($hidden, $model->getTranslationModel()->getHidden());
        }

        $fillable = array_unique(array_diff($fillable, $hidden));

        /**
         * Create collection.
         */
        $elements = new MutableCollection($fillable);

        if ($this->includeDateColumns && count($dates = $model->getDates())) {
            // allow setting specific timestamp: created_at
            if (is_string($this->includeDateColumns)) {
                $dates = array_intersect($dates, [$this->includeDateColumns]);

                $elements = $elements->merge($dates);
            } else {
                // add timestamps group
                $elements->group('dates', function (Group $group) use ($dates) {
                    $group->merge($dates);

                    return $group;
                });
            }
        }

        return $elements->build(
            new GridDecorator($model)
        );
    }
}
