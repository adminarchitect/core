<?php

namespace Terranet\Administrator\Services;

use Czim\Paperclip\Attachment\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Terranet\Administrator\Actions\RemoveSelected;
use Terranet\Administrator\Actions\SaveOrder;
use Terranet\Administrator\AdminRequest;
use Terranet\Administrator\Contracts\Services\CrudActions as CrudActionsContract;
use Terranet\Administrator\Contracts\Services\Saver;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Requests\UpdateRequest;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\ExportsCollection;
use Terranet\Rankable\Rankable;

class CrudActions implements CrudActionsContract
{
    use ExportsCollection;

    /** @var Scaffolding */
    protected $module;

    /** @var AdminRequest */
    protected $request;

    /**
     * CrudActions constructor.
     *
     * @param $module
     * @param  AdminRequest  $request
     */
    public function __construct($module, AdminRequest $request)
    {
        $this->module = $module;
        $this->request = $request;
    }

    /**
     * Default custom list of actions.
     *
     * @return array
     */
    public function actions()
    {
        return [];
    }

    /**
     * Default list of batch actions.
     *
     * @return array
     */
    public function batchActions()
    {
        $actions = [RemoveSelected::class];

        if ($this->module->model() instanceof Rankable) {
            array_push($actions, SaveOrder::class);
        }

        return $actions;
    }

    /**
     * Update item callback.
     *
     * @param               $eloquent
     * @param UpdateRequest $request
     *
     * @throws Exception
     *
     * @return string
     *
     * @internal param $repository
     */
    public function save($eloquent, UpdateRequest $request)
    {
        $saver = $this->module->saver($eloquent, $request);

        return $saver->sync();
    }

    /**
     * Destroy an attachment.
     *
     * @param   $item
     * @param   $attachment
     *
     * @return bool
     */
    public function detachFile(Model $item, $attachment)
    {
        try {
            $item->$attachment = Attachment::NULL_ATTACHMENT;
            $item->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Destroy item callback.
     *
     * @param Model $item
     *
     * @throws \Exception
     *
     * @return string
     */
    public function delete(Model $item)
    {
        if (method_exists($item, 'trashed') && $item->trashed()) {
            return $item->forceDelete();
        }

        return $item->delete();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $method
     * @param        $model
     * @param null $module
     *
     * @return bool
     */
    public function authorize($method, $model = null, $module = null)
    {
        $accessGate = Gate::forUser($user = $this->request->user());
        $module = $module ?: $this->module;
        $model = $model ?: $module->model();
        $method = Str::camel($method);

        if (($policy = $accessGate->getPolicyFor($model)) && method_exists($policy, $method)) {
            return $accessGate->allows($method, $model);
        }

        return true;
    }
}
