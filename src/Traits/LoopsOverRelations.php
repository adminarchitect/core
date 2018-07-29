<?php

namespace Terranet\Administrator\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Terranet\Administrator\Exception;
use function admin\helpers\present;

trait LoopsOverRelations
{
    /**
     * Loops over provided relations to fetch value.
     *
     * @param        $eloquent
     * @param string $name
     * @param array $relations
     * @param bool $format
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function fetchRelationValue($eloquent, $name, array $relations = [], $format = false)
    {
        $object = clone $eloquent;
        while ($relation = array_shift($relations)) {
            // Treat (Has)Many(ToMany|Through) listing relations as "count()" subQuery.
            if ($this->isCountableRelation($relation)) {
                return present($object, $name, $object->$name()->count());
            }

            $object = call_user_func([$orig = $object, $relation]);

            // Treat BelongsToMany form relation as array of values.
            if ($object instanceof BelongsToMany) {
                return array_map(
                    'intval',
                    \DB::table($object->getTable())
                       ->where($this->getQualifiedForeignKeyName($object), $orig->getKey())
                       ->pluck($this->getQualifiedRelatedKeyName($object))
                       ->toArray()
                );
            }

            $object = $object->getResults();
        }

        return ($object && is_object($object))
            ? ($format ? \admin\helpers\eloquent_attribute($object, $name) : $object->$name)
            : null;
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    protected function getQualifiedForeignKeyName($object)
    {
        if (method_exists($object, 'getQualifiedForeignKeyName')) {
            return $object->getQualifiedForeignKeyName();
        }

        if (method_exists($object, 'getQualifiedForeignPivotKeyName')) {
            return $object->getQualifiedForeignPivotKeyName();
        }

        return $object->getForeignKey();
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    protected function getQualifiedRelatedKeyName($object)
    {
        if (method_exists($object, 'getQualifiedRelatedKeyName')) {
            return $object->getQualifiedRelatedKeyName();
        }

        if (method_exists($object, 'getQualifiedRelatedPivotKeyName')) {
            return $object->getQualifiedRelatedPivotKeyName();
        }

        return $object->getOtherKey();
    }

    /**
     * @param $eloquent
     * @param $id
     *
     * @return bool
     */
    protected function hasRelation($eloquent, $id)
    {
        return (method_exists($eloquent, $id) && ($relation = $eloquent->$id())) ? $relation : null;
    }

    /**
     * @param $relation
     *
     * @return bool
     */
    protected function isCountableRelation($relation)
    {
        return is_a($relation, BelongsToMany::class)
            || is_a($relation, HasMany::class)
            || is_a($relation, HasManyThrough::class);
    }
}
