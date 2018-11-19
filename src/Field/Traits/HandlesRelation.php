<?php

namespace Terranet\Administrator\Field\Traits;

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
        return $this->firstWithModel($this->relation()->getRelated());
    }
}
