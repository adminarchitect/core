<?php

namespace Terranet\Administrator\Services;

use Closure;
use DaveJamesMiller\Breadcrumbs\Generator;
use DaveJamesMiller\Breadcrumbs\Manager as BreadcrumbsManager;
use Route;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Services\Breadcrumbs\EloquentPresenter;

class Breadcrumbs
{
    /**
     * Current scaffold module.
     *
     * @var Module
     */
    protected $module;

    /**
     * Breadcrumbs manager.
     *
     * @var BreadcrumbsManager
     */
    protected $manager;

    /**
     * @param BreadcrumbsManager $manager
     * @param Module $module
     */
    public function __construct(BreadcrumbsManager $manager, Module $module)
    {
        $this->module = $module;

        $this->manager = $manager;
        $this->manager->setView('administrator::partials.breadcrumbs');
    }

    /**
     * Render breadcrumbs.
     *
     * @return string
     */
    public function render()
    {
        $action = $this->currentAction();

        // assembly breadcrumbs
        $this->assembly($action);

        return $this->manager->render($action);
    }

    /**
     * Detect current action.
     *
     * @return null|string
     */
    protected function currentAction()
    {
        $action = substr($action = Route::currentRouteAction(), strpos($action, '@') + 1);

        if (!method_exists($this, $action)) {
            $action = 'index';
        }

        return $action;
    }

    /**
     * @param $action
     *
     * @return mixed
     */
    protected function assembly($action)
    {
        return call_user_func_array([$this, $action], []);
    }

    protected function edit()
    {
        $this->index();

        $this->register('edit', function (Generator $breadcrumbs) {
            $breadcrumbs->parent('index');

            $breadcrumbs->push(trans('administrator::module.action.edit', [
                'resource' => $this->module->singular(),
                'instance' => $this->presentEloquent(),
            ]), null);
        });
    }

    protected function index()
    {
        $this->register('index', function (Generator $breadcrumbs) {
            $breadcrumbs->push($this->module->title(), route('scaffold.index', [
                'module' => $this->module->url(),
            ]));
        });
    }

    protected function create()
    {
        $this->index();

        $this->register('create', function (Generator $breadcrumbs) {
            $breadcrumbs->parent('index');
            $breadcrumbs->push(trans('administrator::module.action.create', [
                'resource' => $this->module->singular(),
            ]), null);
        });
    }

    protected function view()
    {
        $this->index();

        $this->register('view', function (Generator $breadcrumbs) {
            $breadcrumbs->parent('index');
            $breadcrumbs->push(trans('administrator::module.action.view', [
                'resource' => $this->module->singular(),
                'instance' => $this->presentEloquent(),
            ]), route('scaffold.view', ['module' => $this->module->url(), 'id' => app('scaffold.model')->getKey()]));
        });
    }

    /**
     * @return string
     */
    protected function presentEloquent()
    {
        if (!$model = app('scaffold.model')) {
            $model = app('scaffold.module')->model();
        }

        return (new EloquentPresenter($model))->present();
    }

    protected function register($name, Closure $callback)
    {
        if (!$this->manager->exists($name)) {
            $this->manager->register($name, $callback);
        }

        return $this;
    }
}
