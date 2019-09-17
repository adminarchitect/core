<?php

namespace Terranet\Administrator\Contracts;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Contracts\Services\Finder;
use Terranet\Administrator\Contracts\Services\Saver;
use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Requests\UpdateRequest;
use Terranet\Administrator\Services\Breadcrumbs;

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
     * @return string
     */
    public function title(): string;

    /**
     * The module url.
     *
     * @return string
     */
    public function url(): string;

    /**
     * Define the list of columns to show.
     *
     * @return Mutable
     */
    public function columns(): Mutable;

    /**
     * Define the class responsive for fetching items.
     *
     * @return null|Finder
     */
    public function finder(): ?Finder;

    /**
     * Breadcrumbs provider.
     *
     * @return null|Breadcrumbs
     */
    public function breadcrumbs(): ?Breadcrumbs;

    /**
     * Define the class responsive for persisting items.
     *
     * @param  Model  $eloquent
     * @param  UpdateRequest  $request
     * @return Saver
     */
    public function saver(Model $eloquent, UpdateRequest $request): Saver;

    /**
     * Actions handler.
     *
     * @return ActionsManager
     */
    public function actions(): ActionsManager;

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
