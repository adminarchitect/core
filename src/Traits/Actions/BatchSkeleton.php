<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Contracts\Auth\Authenticatable as User;

trait BatchSkeleton
{
    /**
     * Check if specified user is authorized to execute this action.
     *
     * @param User $viewer
     * @return bool
     */
    public function authorize(User $viewer)
    {
        return true;
    }

    /**
     * @param $model
     * @return string
     */
    protected function route($model)
    {
        return route('scaffold.batch', app('scaffold.magnet')->with(
            ['module' => app('scaffold.module')->url()]
        )->toArray());
    }

    /**
     * @param $model
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