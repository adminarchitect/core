<?php

namespace Terranet\Administrator\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Terranet\Administrator\Exception;

trait LoopsOverRelations
{
    /**
     * Loops over provided relations to fetch value
     *
     * @param        $eloquent
     * @param string $name
     * @param array $relations
     * @param bool $format
     * @return mixed
     * @throws Exception
     */
    protected function fetchRelationValue($eloquent, $name, array $relations = [], $format = false)
    {
        $object = clone $eloquent;

        while ($relation = array_shift($relations)) {
            $object = call_user_func([$orig = $object, $relation]);

            if ($object instanceof BelongsToMany) {
                return \DB::table($object->getTable())
                          ->where($this->getQualifiedForeignKeyName($object), $orig->getKey())
                          ->pluck($this->getQualifiedRelatedKeyName($object))
                          ->toArray();
            }

            if (!($object instanceof HasOne || $object instanceof BelongsTo || $object instanceof MorphOne)) {
                throw new Exception('Only HasOne and BelongsTo relations supported');
            }

            $object = $object->getResults();
        }

        return ($object && is_object($object))
            ? ($format ? \admin\helpers\eloquent_attribute($object, $name) : $object->$name)
            : null;
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function getQualifiedForeignKeyName($object)
    {
        $foreignKey = method_exists($object, 'getQualifiedForeignKeyName')
            ? $object->getQualifiedForeignKeyName()
            : $object->getForeignKey();

        return $foreignKey;
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function getQualifiedRelatedKeyName($object)
    {
        return method_exists($object, 'getQualifiedRelatedKeyName')
            ? $object->getQualifiedRelatedKeyName()
            : $object->getOtherKey();
    }
}
