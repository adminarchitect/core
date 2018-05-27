<?php

namespace Terranet\Administrator\Traits;

use Illuminate\Contracts\Support\Arrayable;

trait SortsObjectsCollection
{
    /**
     * Sort collection of objects by order or class name.
     *
     * @param $objects
     *
     * @return array
     */
    protected function sortCollection($objects)
    {
        $class = false;

        if ($objects instanceof Arrayable) {
            $class = get_class($objects);

            $objects = $objects->toArray();
        }

        usort($objects, function ($aObj, $bObj) {
            list($aRank, $bRank) = $this->getRanks($aObj, $bObj);

            if ($this->equals($aRank, $bRank)) {
                return $this->sortByName($aObj, $bObj);
            }

            return $this->sortByValue($aRank, $bRank);
        });

        return $class ? $class::make($objects) : $objects;
    }

    /**
     * @param $aRank
     * @param $bRank
     *
     * @return bool
     */
    protected function equals($aRank, $bRank)
    {
        return $aRank === $bRank;
    }

    /**
     * @param $aObj
     * @param $bObj
     *
     * @return int
     */
    protected function sortByName($aObj, $bObj)
    {
        $aName = class_basename($aObj);
        $bName = class_basename($bObj);

        return $aName < $bName ? -1 : 1;
    }

    /**
     * @param $aRank
     * @param $bRank
     *
     * @return int
     */
    protected function sortByValue($aRank, $bRank)
    {
        return $aRank < $bRank ? -1 : 1;
    }

    protected function getRanks($aObj, $bObj)
    {
        $aRank = method_exists($aObj, 'order') ? $aObj->order() : 10;
        $bRank = method_exists($bObj, 'order') ? $bObj->order() : 10;

        return [$aRank, $bRank];
    }
}
