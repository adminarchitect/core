<?php

namespace Terranet\Administrator\Traits;

trait SessionGuardHelper
{
    /**
     * Fetch auth model from Laravel 5.2 auth config.
     */
    protected function guardedModel()
    {
        return ($provider = config('auth.guards.admin.provider'))
            ? config('auth.providers')[$provider]['model']
            : null;
    }

    /**
     * Fetch auth model from Administrator config or Laravel 5.1 auth config.
     *
     * @param $config
     *
     * @return mixed
     */
    protected function authModel($config)
    {
        return $config->get('auth.model', config('auth.model'));
    }

    /**
     * Fetch authentication model.
     *
     * @param $config
     *
     * @return null|mixed
     */
    protected function fetchModel($config)
    {
        if (guarded_auth()) {
            return $this->guardedModel();
        }

        return $this->authModel($config);
    }
}
