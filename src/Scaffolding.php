<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Terranet\Administrator\Contracts\ActionsManager as ActionsManagerContract;
use Terranet\Administrator\Contracts\AutoTranslatable;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Services\Finder as FinderContract;
use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Services\Breadcrumbs;
use Terranet\Administrator\Services\CrudActions;
use Terranet\Administrator\Services\Finder;
use Terranet\Administrator\Services\Saver;
use Terranet\Administrator\Services\Template;
use Terranet\Administrator\Traits\AutoTranslatesInstances;
use Terranet\Administrator\Traits\Module\AllowsNavigation;
use Terranet\Administrator\Traits\Module\HasColumns;

class Scaffolding implements Module, AutoTranslatable
{
    use AllowsNavigation, HasColumns, AutoTranslatesInstances;

    const PAGE_INDEX = 'index';
    const PAGE_VIEW = 'view';
    const PAGE_EDIT = 'edit';

    /**
     * The module Eloquent model class.
     *
     * @return string
     */
    protected $model;

    /**
     * Breadcrumbs provider.
     *
     * @var Breadcrumbs
     */
    protected $breadcrumbs = Breadcrumbs::class;

    /**
     * Service layer responsible for searching items.
     *
     * @var FinderContract
     */
    protected $finder = Finder::class;

    /**
     * Service layer responsible for persisting request.
     *
     * @var \Terranet\Administrator\Contracts\Services\Saver
     */
    protected $saver = Saver::class;

    /**
     * Actions manager class.
     *
     * @var \Terranet\Administrator\Contracts\Services\CrudActions
     */
    protected $actions = CrudActions::class;

    /**
     * View templates provider.
     *
     * @var TemplateProvider
     */
    protected $template = Template::class;

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

    /** @var array */
    protected static $methods = [];

    /** @var Request */
    protected $request;

    /**
     * Scaffolding constructor.
     */
    public function __construct(Request $request)
    {
        if (null === $this->includeDateColumns) {
            $this->includeDateColumns = $this->defaultIncludeDateColumnsValue();
        }

        $this->request = $request;
    }

    /**
     * @param $method
     * @param $arguments
     * @return null|mixed
     */
    public function __call($method, $arguments)
    {
        // Call user-defined method if exists.
        if ($closure = Arr::get(static::$methods, $method)) {
            return \call_user_func_array($closure, $arguments);
        }

        return null;
    }

    /**
     * Extend functionality by adding new methods.
     *
     * @param $name
     * @param $closure
     * @throws Exception
     */
    public static function addMethod($name, $closure)
    {
        if (!(new static())->hasMethod($name)) {
            static::$methods[$name] = $closure;
        }
    }

    /**
     * Disable Actions column totally for Readonly Resources.
     *
     * @return bool
     */
    public function readonly()
    {
        return false;
    }

    /**
     * Check if method exists.
     *
     * @param $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name) || Arr::has(static::$methods, $name);
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
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
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

        return $this->finder;
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function finderInstance(): FinderContract
    {
        $className = $this->finder();

        return once(function () use ($className) {
            $this->columns();

            return new $className($this);
        });
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

        return $this->actions;
    }

    /**
     * @return ActionsManager
     * @throws Exception
     */
    public function actionsManager(): ActionsManagerContract
    {
        $handler = $this->actions();
        $handler = new $handler($this);

        if (!$handler instanceof CrudActions) {
            throw new Exception('Actions handler must implement '.CrudActions::class.' contract');
        }

        return new ActionsManager($handler, $this);
    }

    /**
     * The module Eloquent model.
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getModelClass()
    {
        return $this->model;
    }

    /**
     * Get the full path to class of special type.
     *
     * @param $type
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
