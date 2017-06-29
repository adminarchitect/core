<?php

namespace Terranet\Administrator\Traits\Module;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Services\Widgets\OneToManyRelation;
use Terranet\Administrator\Services\Widgets\OneToOneRelation;
use Terranet\Administrator\Traits\MethodsCollector;

trait HasWidgets
{
    use MethodsCollector;

    /**
     * Return list of widgets (relations)
     * fetched while viewing item details
     *
     * @return array
     */
    public function widgets()
    {
        return $this->scaffoldWidgets();
    }

    protected function scaffoldWidgets()
    {
        /*
        |--------------------------------------------------------------------------
        | Collecting relations
        |--------------------------------------------------------------------------
        |
        | Here we accept only BelongsTo, HasOne, HasMany and BelongsToMany relationships
        | because it allow to us easy iterate such relations.
        |
        */

        $widgets = new Mutable();

        $rank = 0;

        foreach ($this->collectMethods($this->model()) as $method) {
            if ($results = $this->hasCommentFlag('widget', $method)) {
                $relation = call_user_func([app('scaffold.model'), $method->getName()]);

                if (($relation instanceof BelongsTo || $relation instanceof HasOne)) {
                    $widget = new OneToOneRelation($relation);
                } elseif ($relation instanceof HasMany || $relation instanceof BelongsToMany) {
                    $widget = new OneToManyRelation($relation);
                } else {
                    throw new Exception(sprintf("Relations of type '%s' not supported.", class_basename($relation)));
                }

                if (list(/*$flag*/, $tab) = $this->hasCommentFlag('tab', $method)) {
                    $widget->setTab($tab);
                }

                if (list(/*$flag*/, $placement) = $this->hasCommentFlag('placement', $method)) {
                    $widget->setPlacement($placement);
                }

                if (! list(/*$flag*/, $order) = $this->hasCommentFlag('order', $method)) {
                    $rank += 10;
                    $order = $rank;
                }
                $widget->setOrder((int) $order);

                $widgets->push($widget);
            }
        }

        return $widgets;
    }
}
