<?php

namespace Terranet\Administrator\Contracts\Module;

interface Navigable
{
    const MENU_SIDEBAR = 'sidebar';

    const MENU_TOOLS = 'tools';

    const AS_LINK = 'link';

    const AS_HEADER = 'header';

    /**
     * Navigation container which Resource belongs to
     * Available: sidebar, tools.
     *
     * @return mixed
     */
    public function navigableIn();

    /**
     * Add resource to navigation if condition accepts.
     *
     * @return mixed
     */
    public function showIf();

    /**
     * Add resource to navigation as link or header.
     *
     * @return mixed
     */
    public function showAs();

    /**
     * Navigation group which Resource belongs to.
     *
     * @return string
     */
    public function group();

    /**
     * Resource order number.
     *
     * @return int
     */
    public function order();

    /**
     * Attributes assigned to <a> element.
     *
     * @return mixed
     */
    public function linkAttributes();
}
