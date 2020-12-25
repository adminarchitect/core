<?php

namespace Terranet\Administrator\Contracts\Module;

use Illuminate\Http\Request;

interface Navigable
{
    /** @var string */
    const MENU_SIDEBAR = 'sidebar';

    /** @var string */
    const MENU_TOOLS = 'tools';

    /** @var string */
    const AS_LINK = 'link';

    /** @var string */
    const AS_HEADER = 'header';

    /**
     * Navigation container which Resource belongs to
     * Available: sidebar, tools.
     *
     * @return mixed
     */
    public function navigableIn();

    /**
     * Append default params to navigation link.
     * Useful for default filters, scopes, etc...
     *
     * @return array
     */
    public function navigableParams(): array;

    /**
     * Add resource to navigation if condition accepts.
     *
     * @return mixed
     */
    public function showIf(Request $request);

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
