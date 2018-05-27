<?php

namespace Terranet\Administrator\Traits\Actions;

use Illuminate\Database\Eloquent\Model as Eloquent;

trait Skeleton
{
    /**
     * Render action button.
     *
     * @param Eloquent $entity
     *
     * @return string
     */
    public function render(Eloquent $entity = null)
    {
        $action = app('scaffold.module')->url().'-'.$this->action($entity);

        return
<<<OUTPUT
<a data-scaffold-action="{$action}" data-scaffold-key="{$this->entityKey($entity)}" href="{$this->route($entity)}" {$this->attributes($entity)}>
    <i class="fa {$this->icon($entity)}"></i>&nbsp;{$this->name($entity)}
</a>
OUTPUT;
    }

    /**
     * @param Eloquent $entity
     *
     * @return string
     */
    protected function action(Eloquent $entity = null)
    {
        return snake_case(class_basename($this));
    }

    /**
     * Action name.
     *
     * @param Eloquent $entity
     *
     * @return string
     */
    protected function name(Eloquent $entity = null)
    {
        return app('translator')->has($key = $this->translationKey())
            ? trans($key)
            : title_case(str_replace('_', ' ', snake_case(class_basename($this))));
    }

    /**
     * @param Eloquent $entity
     *
     * @return string
     */
    protected function icon(Eloquent $entity = null)
    {
        return 'fa-circle-thin';
    }

    /**
     * @return string
     */
    protected function translationKey()
    {
        $key = sprintf('administrator::actions.%s.%s', app('scaffold.module')->url(), snake_case(class_basename($this)));

        if (!app('translator')->has($key)) {
            $key = sprintf('administrator::actions.global.%s', snake_case(class_basename($this)));
        }

        return $key;
    }

    protected function entityKey($entity = null)
    {
        return $entity ? $entity->getKey() : 'empty';
    }
}
