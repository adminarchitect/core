<?php

namespace Terranet\Administrator\Contracts\Services;

interface TemplateProvider
{
    /**
     * Scaffold layout.
     *
     * @return string
     */
    public function layout();

    /**
     * Scaffold index template.
     *
     * @param  $partial
     *
     * @return mixed array|string
     */
    public function index($partial = 'index');

    /**
     * Scaffold view templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function view($partial = 'index');

    /**
     * Scaffold edit templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function edit($partial = 'index');

    /**
     * Scaffold navigation templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function menu($partial = 'index');

    /**
     * Scaffold partials templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function partials($partial = 'index');

    /**
     * Scaffold scripts templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function scripts($partial = 'index');

    /**
     * Scaffold auth templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function auth($partial = 'index');

    /**
     * Scaffold dashboard templates.
     *
     * @param $partial
     *
     * @return mixed array|string
     */
    public function dashboard($partial = 'index');
}
