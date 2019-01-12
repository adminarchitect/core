<?php

namespace Terranet\Administrator\Services;

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsManager;
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
    }

    /**
     * Render breadcrumbs.
     *
     * @throws \DaveJamesMiller\Breadcrumbs\Exceptions\InvalidBreadcrumbException
     * @throws \DaveJamesMiller\Breadcrumbs\Exceptions\UnnamedRouteException
     * @throws \DaveJamesMiller\Breadcrumbs\Exceptions\ViewNotSetException
     *
     * @return string
     */
    public function render()
    {
        $action = $this->currentAction();

        // assembly breadcrumbs
        $this->assembly($action);

        return $this->manager->view('administrator::partials.breadcrumbs', $action);
    }

    /**
     * Detect current action.
     *
     * @return null|string
     */
    protected function currentAction()
    {
        $action = substr($action = app('router')->currentRouteAction(), strpos($action, '@') + 1);

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
        return \call_user_func_array([$this, $action], []);
    }

    /**
     * Render `Index page` breadcrumbs.
     */
    protected function index()
    {
        $this->manager->register('index', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->push($this->module->title(), route('scaffold.index', [
                'module' => $this->module->url(),
            ]));
        });
    }

    /**
     * Render `Edit page` breadcrumbs.
     */
    protected function edit()
    {
        $this->index();

        $this->manager->register('edit', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->parent('index');

            $breadcrumbs->push(trans('administrator::module.action.edit', [
                'resource' => $this->module->singular(),
                'instance' => $this->presentEloquent(),
            ]), null);
        });
    }

    /**
     * Render `Create page` breadcrumbs.
     */
    protected function create()
    {
        $this->index();

        $this->manager->register('create', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->parent('index');
            $breadcrumbs->push(trans('administrator::module.action.create', [
                'resource' => $this->module->singular(),
            ]), null);
        });
    }

    /**
     * Render `View page` breadcrumbs.
     */
    protected function view()
    {
        $this->index();

        $this->manager->register('view', function (BreadcrumbsGenerator $breadcrumbs) {
            $breadcrumbs->parent('index');
            $breadcrumbs->push(trans('administrator::module.action.view', [
                'resource' => $this->module->singular(),
                'instance' => $this->presentEloquent(),
            ]), route('scaffold.view', ['module' => $this->module->url(), 'id' => app('scaffold.model')->getKey()]));
        });
    }

    /**
     * @return null|string
     */
    protected function presentEloquent(): ?string
    {
        if (!$model = app('scaffold.model')) {
            $model = app('scaffold.module')->model();
        }

        return (new EloquentPresenter($model))->present();
    }
}
