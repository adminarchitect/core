<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Terranet\Administrator\Contracts\Module;

trait BatchSkeleton
{
    /**
     * Check if specified user is authorized to execute this action.
     *
     * @param User $viewer
     * @param null|Model $model
     *
     * @return bool
     */
    public function authorize(User $viewer, ?Model $model = null)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        return $resource->actionsManager()->authorize(
            Str::snake(class_basename($this)),
            $model
        );
    }

    /**
     * @param $model
     *
     * @return string
     */
    protected function route($model)
    {
        return route('scaffold.batch', ['module' => app('scaffold.module')->url()]);
    }

    /**
     * @param $model
     *
     * @return string
     */
    protected function attributes($model)
    {
        return \admin\helpers\html_attributes([
            'data-confirmation' => sprintf('Are you sure you want to %s?', $this->name($model)),
            'data-action' => $this->action($model),
            'class' => 'text-left',
        ]);
    }
}
