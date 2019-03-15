<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Terranet\Administrator\Contracts\Module;

trait ActionSkeleton
{
    /**
     * Check if specified user is authorized to execute this action.
     *
     * @param User $viewer
     * @param Model $model
     *
     * @return bool
     */
    public function authorize(User $viewer, Model $model = null)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        return $resource->actionsManager()->authorize(
            snake_case(class_basename($this)),
            $model
        );
    }

    /**
     * @param Model $model
     *
     * @return string
     */
    protected function route(Model $model = null)
    {
        return route('scaffold.action', [
            'module' => app('scaffold.module'),
            'id' => $model ? $model->getKey() : null,
            'action' => $this->action($model),
        ]);
    }

    /**
     * @param Model $model
     *
     * @return string
     */
    protected function attributes(Model $model = null)
    {
        return \admin\helpers\html_attributes([]);
    }
}
