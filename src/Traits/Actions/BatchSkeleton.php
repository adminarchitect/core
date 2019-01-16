<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Model;

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
        return app('scaffold.actions')->authorize('remove_selected', $model);
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
