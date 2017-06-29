<?php

return [
    'version' => '3.3',

    'prefix' => $prefix = 'cms',

    'title' => "<b>Admin</b> Architect",
    'abbreviation' => 'AA',

    'auth' => [
        'identity' => 'email',
        'credential' => 'password',
        'model' => \App\User::class,
        'conditions' => null,
    ],

    'paths' => [
        # media storage
        'media' => 'media',

        'module' => "Http/Terranet/Administrator/Modules",
        'action' => "Http/Terranet/Administrator/Actions",
        'action_handler' => "Http/Terranet/Administrator/Actions/Handlers",
        'panel' => "Http/Terranet/Administrator/Dashboard",
        'finder' => "Http/Terranet/Administrator/Finders",
        'saver' => "Http/Terranet/Administrator/Savers",
        'column' => "Http/Terranet/Administrator/Decorators",
        'template' => "Http/Terranet/Administrator/Templates",
        'widget' => "Http/Terranet/Administrator/Widgets",
        'badge' => "Http/Terranet/Administrator/Badges",
        'breadcrumbs' => "Http/Terranet/Administrator/Breadcrumbs",
    ],

    'manage_passwords' => true,

    'file_manager' => false,

    'gravatar' => false,

    /**
     * The menu item that should be used as the default landing page of the administrative section
     *
     * @type string
     */
    'home_page' => $prefix,

    /**
     * Basic user validation Rule
     */
    'permission' => \Terranet\Administrator\Auth\SuperAdminRule::class,

    /**
     * Navigation Factory
     */
    'menu' => \Terranet\Administrator\Navigation\Factory::class,

    /**
     * Dashboard Panels Factory
     */
    'dashboard' => \App\Http\Terranet\Administrator\Dashboard\Factory::class,

    'resource' => [
        /**
         * The custom way to resolve module name for custom resources
         * when controller missing Router's $module parameter
         */
        'resolver' => null,

        /**
         * Default segment for module name resolver
         * /admin/pages - admin => 1, pages => 2
         */
        'segment' => 2,
    ],
];
