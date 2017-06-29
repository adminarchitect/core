<?php

namespace Terranet\Administrator\Traits\Form;

use Terranet\Administrator\Traits\LoopsOverRelations;

trait HasRelation
{
    use LoopsOverRelations;

    protected $relation = null;

    public function hasRelation()
    {
        return !is_null($this->relation);
    }

    public function loadRelation()
    {
        list($table) = explode('.', $this->relation);

        return $this->getRepository()->$table();
    }

    public function getRelation()
    {
        return $this->relation;
    }

    public function setRelation($relation = null)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @param $model
     * @return array
     */
    protected function extractValueFromEloquentRelation($model)
    {
        return $this->fetchRelationValue($model, $this->name, $this->relations());
    }

    /**
     * @return array
     * @internal param array $options
     */
    protected function relations()
    {
        return explode('.', $this->relation);
    }
}
