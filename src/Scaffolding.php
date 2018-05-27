<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\AutoTranslatable;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Services\Breadcrumbs;
use Terranet\Administrator\Services\CrudActions;
use Terranet\Administrator\Services\Finder;
use Terranet\Administrator\Services\Saver;
use Terranet\Administrator\Services\Template;
use Terranet\Administrator\Traits\AutoTranslatesInstances;
use Terranet\Administrator\Traits\Module\AllowsNavigation;
use Terranet\Administrator\Traits\Module\DetectsCommentFlag;
use Terranet\Administrator\Traits\Module\HasColumns;
use Terranet\Administrator\Traits\Module\HasWidgets;

class Scaffolding implements Module, AutoTranslatable
{
    use AllowsNavigation, DetectsCommentFlag, HasColumns, HasWidgets, AutoTranslatesInstances;

    /**
     * The module Eloquent model class.
     *
     * @return string
     */
    protected $model;

    /**
     * Breadcrumbs provider.
     *
     * @var
     */
    protected $breadcrumbs = Breadcrumbs::class;

    /**
     * Service layer responsible for searching items.
     *
     * @var
     */
    protected $finder = Finder::class;

    /**
     * Service layer responsible for persisting request.
     *
     * @var
     */
    protected $saver = Saver::class;

    /**
     * Actions manager class.
     *
     * @var
     */
    protected $actions = CrudActions::class;

    /**
     * View templates provider.
     *
     * @var
     */
    protected $template = Template::class;

    /**
     * Query string parameters that should be appended to whole links and forms.
     *
     * @var array
     */
    protected $magnetParams = [];

    /**
     * Include or not columns of Date type in index listing.
     *
     * @var bool
     */
    protected $includeDateColumns;

    /**
     * Global ACL Manager.
     *
     * @var mixed null
     */
    protected $guard;

    protected static $methods = [];

    public function __construct()
    {
        if (null === $this->includeDateColumns) {
            $this->includeDateColumns = $this->defaultIncludeDateColumnsValue();
        }
    }

    public function __call($method, $arguments)
    {
        // Call user-defined method if exists.
        if ($closure = array_get(static::$methods, $method)) {
            return call_user_func_array($closure, $arguments);
        }

        return null;
    }

    /**
     * Extend functionality by adding new methods.
     *
     * @param $name
     * @param $closure
     *
     * @throws Exception
     */
    public static function addMethod($name, $closure)
    {
        if (!(new static())->hasMethod($name)) {
            static::$methods[$name] = $closure;
        }
    }

    /**
     * @return mixed
     */
    public function guard()
    {
        // set the default guard
        if (null === $this->guard) {
            $this->guard = config('administrator.acl.manager');
        }

        if (is_string($this->guard)) {
            $this->guard = new $this->guard($this);
        }

        return $this->guard;
    }

    /**
     * Disable Actions column totally for Readonly Resources.
     *
     * @return bool
     */
    public function hasActions()
    {
        return true;
    }

    /**
     * Check if method exists.
     *
     * @param $name
     *
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name) || array_has(static::$methods, $name);
    }

    /**
     * The module Templates manager.
     *
     * @return string
     */
    public function template()
    {
        if (class_exists($file = $this->getQualifiedClassNameOfType('Templates'))) {
            return $file;
        }

        if (list(/*$flag*/, $value) = $this->hasCommentFlag('template')) {
            return $value;
        }

        return $this->template;
    }

    /**
     * @return Model
     */
    public function model()
    {
        static $model = null;

        if (null === $model && ($class = $this->getModelClass())) {
            $model = new $class();
        }

        return $model;
    }

    /**
     * Define the class responsive for fetching items.
     *
     * @return mixed
     */
    public function finder()
    {
        if (class_exists($file = $this->getQualifiedClassNameOfType('Finders'))) {
            return $file;
        }

        if (list(/*$flag*/, $value) = $this->hasCommentFlag('finder')) {
            return $value;
        }

        return $this->finder;
    }

    /**
     * Define the class responsive for persisting items.
     *
     * @return mixed
     */
    public function saver()
    {
        if (class_exists($file = $this->getQualifiedClassNameOfType('Savers'))) {
            return $file;
        }

        if (list(/*$flag*/, $saver) = $this->hasCommentFlag('saver')) {
            return $saver;
        }

        return $this->saver;
    }

    /**
     * Breadcrumbs provider
     * First parse Module doc block for provider declaration.
     *
     * @return mixed
     */
    public function breadcrumbs()
    {
        if (class_exists($file = $this->getQualifiedClassNameOfType('Breadcrumbs'))) {
            return $file;
        }

        if (list(/*$flag*/, $value) = $this->hasCommentFlag('breadcrumbs')) {
            return $value;
        }

        return $this->breadcrumbs;
    }

    /**
     * Define the Actions provider - object responsive for
     * CRUD operations, Export, etc...
     * as like as checks action permissions.
     *
     * @return mixed
     */
    public function actions()
    {
        if (class_exists($file = $this->getQualifiedClassNameOfType('Actions'))) {
            return $file;
        }

        if (list(/*$flag*/, $value) = $this->hasCommentFlag('actions')) {
            return $value;
        }

        return $this->actions;
    }

    /**
     * Return magnet params.
     *
     * @return array
     */
    public function magnetParams()
    {
        return (array) $this->magnetParams;
    }

    /**
     * The module Eloquent model.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getModelClass()
    {
        if (list(/*$flag*/, $value) = $this->hasCommentFlag('model')) {
            return $value;
        }

        return $this->model;
    }

    /**
     * Get the full path to class of special type.
     *
     * @param $type
     *
     * @return string
     */
    protected function getQualifiedClassNameOfType($type)
    {
        return app()->getNamespace()."Http\\Terranet\\Administrator\\{$type}\\".class_basename($this);
    }

    /**
     * @return mixed
     */
    protected function defaultIncludeDateColumnsValue()
    {
        return config(
            "administrator.grid.timestamps.{$this->url()}",
            config('administrator.grid.timestamps.enabled')
        );
    }
}
