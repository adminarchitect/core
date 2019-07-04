<?php

namespace Terranet\Administrator\Field\Traits;

use Terranet\Administrator\Architect;
use Terranet\Administrator\Contracts\Module;

trait HandlesRelation
{
    /**
     * @return mixed
     */
    public function relation()
    {
        return \call_user_func([$this->model, $this->id]);
    }

    /**
     * Finds a module this relation belongs to.
     *
     * @return null|
     */
    public function relationModule(): ?Module
    {
        return Architect::resourceByEntity(
            $this->relation()->getRelated()
        );
    }

    /**
     * @param $relation
     * @return mixed
     */
    public function getForeignKey($relation)
    {
        if (method_exists($relation, 'getForeignKey')) {
            return $relation->getForeignKey();
        }

        if (method_exists($relation, 'getForeignKeyName')) {
            return $relation->getForeignKeyName();
        }

        if (method_exists($relation, 'getForeignPivotKeyName')) {
            return $relation->getForeignPivotKeyName();
        }

        throw new \Exception("Unable to resolve foreign key.");
    }
}
