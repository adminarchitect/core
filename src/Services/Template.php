<?php

namespace Terranet\Administrator\Services;

use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Contracts\Services\TemplateProvider;

class Template implements TemplateProvider
{
    /**
     * Scaffold layout.
     *
     * @param string $layout
     *
     * @return string
     */
    public function layout($layout = 'app')
    {
        return config('administrator.layouts.'.$layout, 'administrator::layouts.'.$layout);
    }

    /**
     * Scaffold index templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function index($partial = 'index')
    {
        $partials = $this->map(
            'index',
            ['index', 'create', 'export', 'filters', 'scopes', 'header', 'batch', 'row', 'scripts', 'paginator']
        );

        return null === $partial ? $partials : $partials[$partial];
    }

    /**
     * Scaffold media templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function media($partial = 'index')
    {
        $partials = $this->map(
            'media',
            ['index']
        );

        return null === $partial ? $partials : $partials[$partial];
    }

    /**
     * Scaffold translations templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function translations($partial = 'index')
    {
        $partials = $this->map(
            'translations',
            ['index']
        );

        return null === $partial ? $partials : $partials[$partial];
    }

    /**
     * Scaffold view templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function view($partial = 'index')
    {
        $partials = $this->map('view', [
            'index',
            'model',
            'create',
        ]);

        return null === $partial ? $partials : $partials[$partial];
    }

    /**
     * Scaffold edit templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function edit($partial = 'index')
    {
        $partials = $this->map('edit', ['index', 'actions', 'row', 'scripts', 'create']);

        return null === $partial ? $partials : $partials[$partial];
    }

    public function menu($partial = 'sidebar')
    {
        $partials = $this->map('menus', [Navigable::MENU_SIDEBAR, Navigable::MENU_TOOLS]);

        return null === $partial ? $partials : $partials[$partial];
    }

    public function partials($partial = 'messages')
    {
        $partials = $this->map('partials', ['messages', 'breadcrumbs']);

        return null === $partial ? $partials : $partials[$partial];
    }

    public function scripts($partial = null)
    {
        $partials = $this->map('scripts', ['listeners', 'editors']);

        return null === $partial ? $partials : $partials[$partial];
    }

    public function auth($partial = 'login')
    {
        $partials = $this->map('auth', ['login']);

        return null === $partial ? $partials : $partials[$partial];
    }

    public function dashboard($partial = null)
    {
        $partials = $this->map('dashboard', ['database', 'members', 'google_analytics']);

        return null === $partial ? $partials : $partials[$partial];
    }

    /**
     * @param $namespace
     * @param array $views
     *
     * @return array
     */
    protected function map($namespace, array $views = [])
    {
        return array_merge(
            ['index' => "administrator::{$namespace}"],
            array_build($views, function ($key, $view) use ($namespace) {
                return [$view, "administrator::{$namespace}.{$view}"];
            })
        );
    }
}
