<?php

namespace Terranet\Administrator\Columns;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
     * Set element template.
     *
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get element template.
     *
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Eloquent $eloquent
     * @return mixed
     */
    public function render(Eloquent $eloquent)
    {
        /**
         * Fetch value from Eloquent instance.
         */
        if (!$this->template) {
            return $this->fetchValue($eloquent);
        }

        /**
         * Handle Renderable decorators.
         *
         * @example: (new CellElement($id))->setDecorator(view('users.comments'));
         */
        if ($this->template instanceof Renderable) {
            return $this->template->with([
                'renderable' => $this,
                'eloquent' => $eloquent,
            ]);
        }

        /**
         * Handle instance of CellDecorators.
         *
         * @example: (new CellElement($id))->setDecorator(new BooleanDecorator);
         */
        if ($this->template instanceof CellDecorator) {
            $closure = $this->template->getDecorator();

            return $closure($eloquent);
        }

        /**
         * Handle closure-based decorators.
         *
         * @example: (new CellElement($id))->setDecorator(function ($row) { return $row->id; });
         */
        if (is_callable($closure = $this->template)) {
            return $closure($eloquent);
        }

        /**
         * Handle pattern-based decorators.
         *
         * @example: (new CellElement($id))->setDecorator('<a href="mailto:(:email)">(:email)</a>');
         */
        return preg_replace_callback('~\(\:([a-z0-9\_]+)\)~si', function ($matches) use ($eloquent) {
            $field = $matches[1];

            return \admin\helpers\eloquent_attribute($eloquent, $field);
        }, $this->template);
    }

    /**
     * Fetch element value from eloquent
     *
     * @param $eloquent
     * @return mixed
     */
    protected function fetchValue($eloquent)
    {
        $id = $this->id();

        # Treat (Has)Many(ToMany|Through) relations as "count()" subQuery.
        if (($relation = $this->hasRelation($eloquent, $id)) && $this->isCountableRelation($relation)) {
            return $this->fetchRelationValue($eloquent, $id, [$id => $relation], true);
        }

        if ($this->relations) {
            return $this->fetchRelationValue($eloquent, $id, $this->relations, true);
        }

        return \admin\helpers\eloquent_attribute($eloquent, $id);
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
     * @return $this
     */
    public function setStandalone($flag = true)
    {
        $this->standalone = (bool)$flag;

        return $this;
    }

    public function isGroup()
    {
        return false;
    }
}