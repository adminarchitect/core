<?php

namespace Terranet\Administrator\Providers\Handlers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Events\RouteMatched;
use Terranet\Administrator\Traits\SessionGuardHelper;

class PasswordsManager
{
    use SessionGuardHelper;

    /**
     * @var Repository
     */
    private $config;

    /**
     * PasswordsManager constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function handle()
    {
        app('router')->matched(function (RouteMatched $event) {
            if (!$this->isAdminArea($event)) {
                return false;
            }

            if ($manage = $this->config->get('administrator.manage_passwords')) {
                if ($model = $this->fetchModel($this->config)) {
                    $model::saving(function ($user) {
                        if (!empty($user->password) && $user->isDirty('password')) {
                            $user->password = bcrypt($user->password);
                        }
                    });
                }
            }
        });
    }

    /**
     * Check if running under admin area.
     *
     * @param RouteMatched $event
     *
     * @return bool
     */
    protected function isAdminArea(RouteMatched $event)
    {
        if ($action = $event->route->getAction()) {
            return config('administrator.prefix') === array_get($action, 'prefix');
        }

        return false;
    }
}
