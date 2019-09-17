<?php

namespace Terranet\Administrator\Traits\Module;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Collection\Group;
use Terranet\Administrator\Collection\Mutable as MutableCollection;
use Terranet\Administrator\Dashboard\Manager;
use Terranet\Administrator\Decorators\Grid as GridDecorator;
use Terranet\Translatable\Translatable;

trait HasColumns
{
    /**
     * Fetch scaffold columns.
     *
     * @return MutableCollection
     */
    public function columns(): MutableCollection
    {
        return $this->scaffoldColumns();
    }

    /**
     * Define the list of attributes visible on View model page.
     *
     * @return MutableCollection
     */
    public function viewColumns(): MutableCollection
    {
        return $this->scaffoldColumns();
    }

    /**
     * List of widgets.
     *
     * @return Manager
     */
    public function widgets(): Manager
    {
        return new Manager();
    }

    /**
     * List of cards.
     *
     * @return Manager
     */
    public function cards(): Manager
    {
        return new Manager();
    }

    /**
     * Scaffold columns.
     *
     * @return MutableCollection
     */
    protected function scaffoldColumns(): MutableCollection
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
    protected function collectColumns(Model $model = null): MutableCollection
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

        $elements = new MutableCollection($fillable);

        if (property_exists($this, 'includeDateColumns')
            && $this->includeDateColumns
            && count($dates = $model->getDates())) {
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
