<?php

namespace Terranet\Administrator;

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
    protected $actions = null;

    /**
     * List of global actions.
     *
     * @var array
     */
    protected $globalActions = null;

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
     * Parse handler class for per-item and global actions.
     *
     * @return Collection
     */
    protected function scaffoldActions()
    {
        return (new Collection($this->service->actions()));
    }

    /**
     * Parse handler class for per-item and global actions.
     *
     * @return Collection
     */
    protected function scaffoldBatch()
    {
        return (new Collection($this->service->batchActions()));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $method
     * @param $model
     *
     * @return bool
     */
    public function authorize($method, $model = null)
    {
        return $this->service->authorize($method, $model, $this->module);
    }

    /**
     * Call handler method.
     *
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function exec($method, array $arguments = [])
    {
        // execute custom action
        if (starts_with($method, 'action::')) {
            $handler = $this->scaffoldActions()->find(
                str_replace('action::', '', $method)
            );

            return call_user_func_array([$handler, 'handle'], $arguments);
        }

        // Execute batch action
        if (starts_with($method, 'batch::')) {
            $handler = $this->scaffoldBatch()->find(
                str_replace('batch::', '', $method)
            );

            return call_user_func_array([$handler, 'handle'], $arguments);
        }

        // Execute CRUD action
        return call_user_func_array([$this->service, $method], (array) $arguments);
    }
}
