<?php

namespace Terranet\Administrator\Contracts;

use Terranet\Administrator\Contracts\Services\Finder;

interface Module
{
    /**
     * The module Eloquent model.
     *
     * @return mixed
     */
    public function model();

    /**
     * The module title.
     *
     * @return mixed
     */
    public function title();

    /**
     * The module url.
     *
     * @return mixed
     */
    public function url();

    /**
     * Define the list of columns to show.
     *
     * @return mixed
     */
    public function columns();

    /**
     * Define the class responsive for fetching items.
     *
     * @return mixed
     */
    public function finder();

    /**
     * Define the class responsive for fetching items.
     *
     * @return mixed
     */
    public function finderInstance(): Finder;

    /**
     * Breadcrumbs provider.
     *
     * @return mixed
     */
    public function breadcrumbs();

    /**
     * Define the class responsive for persisting items.
     *
     * @return mixed
     */
    public function saver();

    /**
     * Actions handler.
     *
     * @return mixed
     */
    public function actions();

    /**
     * Actions handler.
     *
     * @return ActionsManager
     */
    public function actionsManager(): ActionsManager;

    /**
     * The module Templates manager.
     *
     * @return string
     */
    public function template();
}
