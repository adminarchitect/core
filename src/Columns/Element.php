<?php

namespace Terranet\Administrator\Columns;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Terranet\Administrator\Columns\Decorators\CellDecorator;
use Terranet\Administrator\Traits\Collection\ElementContainer;
use Terranet\Administrator\Traits\LoopsOverRelations;

class Element extends ElementContainer
{
    use LoopsOverRelations;

    protected $template;

    protected $relations;

    protected $standalone = false;

    protected $keepOriginalID = false;

    /**
     * Set element display template.
     *
     * @param $template
     *
     * @return $this
     */
    public function display($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param Eloquent $eloquent
     *
     * @return mixed
     */
    public function render(Eloquent $eloquent)
    {
        // Fetch value from Eloquent instance.
        if (!$this->template) {
            return $this->fetchValue($eloquent);
        }

        /*
         * Handle \Illuminate\Contracts\Support\Renderable (view(<template>)) decorators.
         *
         * @example: $element->display(view('users.comments'));
         */
        if ($this->template instanceof Renderable) {
            return $this->template->with([
                'renderable' => $this,
                'eloquent' => $eloquent,
            ]);
        }

        /*
         * Handle instance of CellDecorators.
         *
         * @example: $element->display(new BooleanDecorator);
         */
        if ($this->template instanceof CellDecorator) {
            $closure = $this->template->getDecorator();

            return $closure($eloquent);
        }

        /**
         * Handle closure-based decorators.
         *
         * @example: (new CellElement($id))->setDecorator(function() { return $this->id; });
         */
        if (($closure = $this->template) instanceof \Closure) {
            return $closure->call($eloquent, $eloquent);
        }

        /*
         * Handle pattern-based decorators.
         *
         * @example: $element->display('<a href="mailto:(:email)">(:email)</a>');
         */
        return preg_replace_callback('~\(\:([a-z0-9\_]+)\)~si', function ($matches) use ($eloquent) {
            $field = $matches[1];

            return \admin\helpers\eloquent_attribute($eloquent, $field);
        }, $this->template);
    }

    /**
     * Check if column is standalone.
     *
     * @return bool
     */
    public function standalone()
    {
        return $this->standalone;
    }

    /**
     * Set element Standalone flag.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setStandalone($flag = true)
    {
        $this->standalone = (bool) $flag;

        return $this;
    }

    public function isGroup()
    {
        return false;
    }

    /**
     * Make column sortable.
     *
     * @param null|\Closure $callback
     *
     * @return $this
     */
    public function sortable(\Closure $callback = null)
    {
        return tap($this, function ($element) use ($callback) {
            app('scaffold.module')->addSortable(
                $element->id(),
                $callback
            );
        });
    }

    /**
     * Remove column from Sortable collection.
     *
     * @return $this
     */
    public function unSortable()
    {
        return tap($this, function ($element) {
            app('scaffold.module')->removeSortable($element->id());
        });
    }

    /**
     * Fetch element value from eloquent.
     *
     * @param $eloquent
     *
     * @return mixed
     */
    protected function fetchValue($eloquent)
    {
        $id = $this->id();

        // Treat (Has)Many(ToMany|Through) relations as "count()" subQuery.
        if (($relation = $this->hasRelation($eloquent, $id)) && $this->isCountableRelation($relation)) {
            return $this->fetchRelationValue($eloquent, $id, [$id => $relation], true);
        }

        if ($this->relations) {
            return $this->fetchRelationValue($eloquent, $id, $this->relations, true);
        }

        return \admin\helpers\eloquent_attribute($eloquent, $id);
    }
}
