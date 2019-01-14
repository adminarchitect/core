<?php

namespace Terranet\Administrator\Services;

use Czim\Paperclip\Attachment\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Terranet\Administrator\Actions\RemoveSelected;
use Terranet\Administrator\Actions\SaveOrder;
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

    /**
     * @var Scaffolding
     */
    protected $module;

    protected static $responses = [];

    public function __construct($module)
    {
        $this->module = $module;
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
        $saver = app('scaffold.module')->saver();
        $saver = new $saver($eloquent, $request);

        if (!$saver instanceof Saver) {
            throw new Exception('Saver must implement '.Saver::class.' contract');
        }

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
        $module = $module ?: app('scaffold.module');
        $model = $model ?: $module->model();
        $method = camel_case($method);

        if (($policy = Gate::getPolicyFor($model)) && method_exists($policy, $method)) {
            return Gate::allows($method, $model);
        }

        return true;
    }
}
