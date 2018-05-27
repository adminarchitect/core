<?php

namespace Terranet\Administrator\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as AppEventServiceProvider;

class EventServiceProvider extends AppEventServiceProvider
{
    protected $handlers = [
        \Terranet\Administrator\Providers\Handlers\PasswordsManager::class,
        \Terranet\Administrator\Providers\Handlers\RouteManager::class,
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function boot(DispatcherContract $events = null)
    {
        parent::boot($events);

        foreach ($this->handlers as $handler) {
            app($handler)->handle();
        }
    }
}
