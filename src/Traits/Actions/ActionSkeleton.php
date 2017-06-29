<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Foundation\Auth\User;

trait ActionSkeleton
{
    /**
     * Check if specified user is authorized to execute this action.
     *
     * @param User $viewer
     * @param Eloquent $entity
     * @return bool
     */
    public function authorize(User $viewer, Eloquent $entity = null)
    {
        return true;
    }

    /**
     * @param Eloquent $entity
     * @return string
     */
    protected function route(Eloquent $entity = null)
    {
        return route('scaffold.action', [
            'module' => app('scaffold.module'),
            'id' => $entity ? $entity->getKey() : null,
            'action' => $this->action($entity),
        ]);
    }

    /**
     * @param Eloquent $entity
     * @return string
     */
    protected function attributes(Eloquent $entity = null)
    {
        return \admin\helpers\html_attributes([
            //
        ]);
    }
}