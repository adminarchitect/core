<?php

namespace Terranet\Administrator\Actions;

use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

class Collection extends BaseCollection
{
    public function __construct($items = [])
    {
        // initialize action classes only at first execution.
        if (\is_array($items)) {
            $items = array_map(function ($handler) {
                return new $handler();
            }, $items);
        }

        parent::__construct($items);
    }

    /**
     * Find action by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function find($name)
    {
        return $this->first(function ($action) use ($name) {
            return class_basename($action) === Str::studly($name);
        });
    }

    /**
     * Leave actions which User $user is authorized to execute.
     *
     * @param null|User $user
     * @param null|Model $model
     *
     * @return static
     */
    public function authorized(?User $user = null, Model $model = null)
    {
        $user = $user ?: auth('admin')->user();
        $model = $model ?: app('scaffold.module')->model();

        return $this->filter(function ($action) use ($user, $model) {
            // authorize action only if action allows it.
            if (method_exists($action, 'authorize')) {
                return $action->authorize($user, $model);
            }

            return true;
        });
    }
}
