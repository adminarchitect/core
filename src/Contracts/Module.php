<?php

namespace Terranet\Administrator\Contracts;

use Terranet\Administrator\Contracts\Services\Finder;
use Terranet\Administrator\Contracts\Services\TemplateProvider;

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
     * @return null|Finder
     */
    public function finder(): ?Finder;

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
     * @return null|TemplateProvider
     */
    public function template(): ?TemplateProvider;

    /**
     * Filters & Scopes handler.
     *
     * @return null|Filter
     */
    public function filter(): ?Filter;
}
