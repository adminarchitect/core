<?php

namespace Terranet\Administrator\Traits\Module;

use Coduo\PHPHumanizer\StringHumanizer;
use Terranet\Administrator\Contracts\Module\Navigable;

trait AllowsNavigation
{
    /**
     * The module singular title
     *
     * @return mixed
     */
    public function singular()
    {
        return str_singular($this->title());
    }

    /**
     * The module title
     *
     * @return mixed
     */
    public function title()
    {
        return $this->translator()->has($key = $this->translationKey())
            ? trans($key)
            : StringHumanizer::humanize(class_basename($this));
    }

    /**
     * Navigation container which Resource belongs to
     *
     * Available: sidebar, tools
     *
     * @return mixed
     */
    public function navigableIn()
    {
        return Navigable::MENU_SIDEBAR;
    }

    /**
     * Add resource to navigation if condition accepts
     *
     * @return mixed
     */
    public function showIf()
    {
        return true;
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
     * Add resource to navigation as link or header
     *
     * @return mixed
     */
    public function showAs()
    {
        return Navigable::AS_LINK;
    }

    /**
     * Navigation group which module belongs to
     *
     * @return string
     */
    public function group()
    {
        return null;
    }

    /**
     * Resource order number
     *
     * @return int
     */
    public function order()
    {
        return null;
    }

    /**
     * Attributes assigned to <a> element
     *
     * @return mixed
     */
    public function linkAttributes()
    {
        return ['icon' => null, 'id' => $this->url()];
    }

    /**
     * The module url
     *
     * @return mixed
     */
    public function url()
    {
        return snake_case(class_basename($this));
    }

    /**
     * Cast to string
     * Make module Routable.
     * It allows referencing module object while generating routes
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->url();
    }

    public function translationKey()
    {
        return sprintf('administrator::module.resources.%s', $this->url());
    }
}
