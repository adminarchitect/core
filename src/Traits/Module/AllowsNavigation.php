<?php

namespace Terranet\Administrator\Traits\Module;

use Illuminate\Support\Str;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Contracts\Module\Navigable;

trait AllowsNavigation
{
    /**
     * Cast to string
     * Make module Routable.
     * It allows referencing module object while generating routes.
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->url();
    }

    /**
     * The module singular title.
     *
     * @return mixed
     */
    public function singular()
    {
        return Str::singular($this->title());
    }

    /**
     * The module title.
     *
     * @return string
     */
    public function title(): string
    {
        return trans()->has($key = $this->translationKey())
            ? trans($key)
            : Architect::humanize($this);
    }

    /**
     * Navigation container which Resource belongs to.
     * Available: sidebar, tools
     *
     * @return mixed
     */
    public function navigableIn()
    {
        return Navigable::MENU_SIDEBAR;
    }

    /**
     * Append default params to navigation link.
     * Useful for default filters, scopes, etc...
     *
     * @return array
     */
    public function navigableParams(): array
    {
        return [];
    }

    /**
     * Add resource to navigation if condition accepts.
     *
     * @return mixed
     */
    public function showIf()
    {
        return ($guard = $this->guard()) && method_exists($guard, 'showIf')
            ? $guard->showIf()
            : true;
    }

    /**
     * Appends count of items to a navigation.
     *
     * @return bool
     */
    public function appendCount()
    {
        return null;
    }

    /**
     * Add resource to navigation as link or header.
     *
     * @return mixed
     */
    public function showAs()
    {
        return Navigable::AS_LINK;
    }

    /**
     * Navigation group which module belongs to.
     *
     * @return string
     */
    public function group()
    {
        return trans('administrator::module.groups.resources');
    }

    /**
     * Resource order number.
     *
     * @return int
     */
    public function order()
    {
        return null;
    }

    /**
     * Attributes assigned to <a> element.
     *
     * @return mixed
     */
    public function linkAttributes()
    {
        return ['icon' => null, 'id' => $this->url()];
    }

    /**
     * The module url.
     *
     * @return string
     */
    public function url(): string
    {
        return Str::snake(class_basename($this));
    }

    public function translationKey()
    {
        return sprintf('administrator::module.resources.%s', $this->url());
    }
}
