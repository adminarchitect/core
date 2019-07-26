<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Terranet\Administrator\Actions\Collection;
use Terranet\Administrator\Contracts\ActionsManager as ActionsManagerContract;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Services\CrudActions;

class ActionsManager implements ActionsManagerContract
{
    /**
     * @var CrudActions
     */
    protected $service;

    /**
     * @var Module
     */
    protected $module;

    /**
     * List of item-related actions.
     *
     * @var array
     */
    protected $actions;

    /**
     * List of global actions.
     *
     * @var array
     */
    protected $globalActions;

    /**
     * Check if resource is readonly - has no actions.
     *
     * @var null|bool
     */
    protected $readonly;

    /**
     * ActionsManager constructor.
     *
     * @param  CrudActions  $service
     * @param  Module  $module
     */
    public function __construct(CrudActions $service, Module $module)
    {
        $this->service = $service;

        $this->module = $module;
    }

    /**
     * Fetch module's single (per item) actions.
     *
     * @return Collection
     */
    public function actions()
    {
        return $this->scaffoldActions();
    }

    /**
     * Fetch module's batch actions.
     *
     * @return Collection
     */
    public function batch()
    {
        return $this->scaffoldBatch();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $ability
     * @param $model
     *
     * @return bool
     */
    public function authorize($ability, ?Model $model = null)
    {
        // for most cases it is enough to set
        // permissions in Resource object.
        if (method_exists($this->module, $abilityMethod = 'can'.title_case(camel_case($ability)))) {
            $user = auth('admin')->user();
            $cacheID = $user->getAuthIdentifier().':'.$ability.($model ? '_'.$model->getKey() : '');

            return Cache::remember($cacheID, 1, function () use ($user, $ability, $model) {
                $abilityMethod = 'can'.title_case(camel_case($ability));

                return $this->module->$abilityMethod($user, $ability, $model);
            });
        }

        // Ask Actions Service for action permissions.
        return $this->service->authorize($ability, $model, $this->module);
    }

    /**
     * Checks if resource has no Actions at all / Readonly mode.
     */
    public function readonly()
    {
        if (null === $this->readonly) {
            $this->readonly = false;

            // check for <Module>::readonly() method.
            if (method_exists($this->module, 'readonly')) {
                $this->readonly = $this->module->readonly();
            }
        }

        return $this->readonly;
    }

    /**
     * Call handler method.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function exec(string $method, array $arguments = [])
    {
        // execute custom action
        if (starts_with($method, 'action::')) {
            $handler = $this->scaffoldActions()->find(
                str_replace('action::', '', $method)
            );

            return \call_user_func_array([$handler, 'handle'], $arguments);
        }

        // Execute batch action
        if (starts_with($method, 'batch::')) {
            $handler = $this->scaffoldBatch()->find(
                str_replace('batch::', '', $method)
            );

            return \call_user_func_array([$handler, 'handle'], $arguments);
        }

        // Execute CRUD action
        return \call_user_func_array([$this->service, $method], (array) $arguments);
    }

    /**
     * Parse handler class for per-item and global actions.
     *
     * @return Collection
     */
    protected function scaffoldActions()
    {
        return new Collection($this->service->actions());
    }

    /**
     * Parse handler class for per-item and global actions.
     *
     * @return Collection
     */
    protected function scaffoldBatch()
    {
        return new Collection($this->service->batchActions());
    }
}
