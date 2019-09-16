<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

trait Skeleton
{
    /**
     * @return string
     */
    protected function type(): string
    {
        return 'primary';
    }

    /**
     * Render action button.
     *
     * @param  Eloquent  $entity
     * @return string
     */
    public function render(Eloquent $entity = null)
    {
        $action = app('scaffold.module')->url().'-'.$this->action($entity);
        $icon = ($i = $this->icon($entity)) ? "<i class=\"fa {$i}\"></i>" : '';

        return
            <<<OUTPUT
<a data-scaffold-action="{$action}" data-scaffold-key="{$this->entityKey($entity)}" href="{$this->route($entity)}" {$this->attributes($entity)}>
    {$icon}<span>{$this->name($entity)}</span>
</a>
OUTPUT;
    }

    /**
     * Render action button.
     *
     * @param  Eloquent  $entity
     * @return string
     */
    public function renderBtn(Eloquent $entity = null)
    {
        $action = app('scaffold.module')->url().'-'.$this->action($entity);
        $icon = ($i = $this->icon($entity)) ? "<i class=\"fa {$i}\"></i>" : '';

        return
            <<<OUTPUT
<a class="btn btn-{$this->type()}" data-scaffold-action="{$action}" data-scaffold-key="{$this->entityKey($entity)}" href="{$this->route($entity)}" {$this->attributes($entity)}>
    {$icon}<span>{$this->name($entity)}</span>
</a>
OUTPUT;
    }

    /**
     * @param  Eloquent  $entity
     * @return string
     */
    public function action(Eloquent $entity = null)
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Action name.
     *
     * @param  Eloquent  $entity
     * @return string
     */
    public function name(Eloquent $entity = null)
    {
        return app('translator')->has($key = $this->translationKey())
            ? trans($key)
            : Str::title(str_replace('_', ' ', Str::snake(class_basename($this))));
    }

    /**
     * @param  Eloquent  $entity
     * @return string
     */
    protected function icon(Eloquent $entity = null)
    {
        return 'fa-circle-thin';
    }

    /**
     * @return string
     */
    protected function translationKey(): string
    {
        $key = sprintf('administrator::actions.%s.%s', app('scaffold.module')->url(), Str::snake(class_basename($this)));

        if (!app('translator')->has($key)) {
            $key = sprintf('administrator::actions.global.%s', Str::snake(class_basename($this)));
        }

        return $key;
    }

    /**
     * @param  null  $entity
     * @return string
     */
    protected function entityKey($entity = null): string
    {
        return $entity ? $entity->getKey() : 'empty';
    }
}
