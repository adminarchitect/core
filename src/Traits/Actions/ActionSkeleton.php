<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Terranet\Administrator\Contracts\Module;

trait ActionSkeleton
{
    /**
     * Check if specified user is authorized to execute this action.
     *
     * @param  User  $viewer
     * @param  Model  $model
     * @return bool
     */
    public function authorize(User $viewer, Model $model = null)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        return $resource->actionsManager()->authorize(
            Str::snake(class_basename($this)),
            $model
        );
    }

    /**
     * @param  Model  $model
     * @return string
     */
    public function route(Model $model = null)
    {
        return route('scaffold.action', [
            'module' => app('scaffold.module'),
            'id' => $model ? $model->getKey() : null,
            'action' => $this->action($model),
        ]);
    }

    /**
     * Confirmation message, if required.
     *
     * @return mixed null|string
     */
    protected function confirmationMessage(): ?string
    {
        return null;
    }

    /**
     * Hide an action from index page.
     *
     * @return bool
     */
    public function hideFromIndex(): bool
    {
        return false;
    }

    /**
     * Hide an action from view page.
     *
     * @return bool
     */
    public function hideFromView(): bool
    {
        return false;
    }

    /**
     * @param  Model  $model
     * @return string
     */
    protected function attributes(Model $model = null)
    {
        $attributes = [];
        if ($msg = $this->confirmationMessage()) {
            $attributes["onclick"] = "return window.confirm('{$msg}')";
        }

        return \admin\helpers\html_attributes($attributes);
    }
}
