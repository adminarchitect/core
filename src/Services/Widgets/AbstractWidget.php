<?php

namespace Terranet\Administrator\Services\Widgets;

use Terranet\Administrator\Exception;

class AbstractWidget
{
    const PLACEMENT_MAIN_TOP     = 'main-top';
    const PLACEMENT_MAIN_BOTTOM  = 'main-bottom';
    const PLACEMENT_SIDEBAR      = 'sidebar';
    const PLACEMENT_MODEL        = 'model';

    const TAB_DEFAULT            = "General";

    protected $placement = self::PLACEMENT_MAIN_BOTTOM;

    protected $order = 10;

    protected $tab = self::TAB_DEFAULT;

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param string $placement
     * @return $this
     * @throws Exception
     */
    public function setPlacement($placement)
    {
        $this->validatePlacement($placement);

        $this->placement = $placement;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @param string $tab
     * @return $this
     */
    public function setTab($tab)
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * @return array
     */
    protected function allowedPlacements()
    {
        return [
            static::PLACEMENT_MAIN_TOP,
            static::PLACEMENT_MAIN_BOTTOM,
            static::PLACEMENT_MODEL,
            static::PLACEMENT_SIDEBAR
        ];
    }

    /**
     * @param $placement
     * @throws Exception
     */
    protected function validatePlacement($placement)
    {
        if (! in_array($placement, $allowed = $this->allowedPlacements())) {
            throw new Exception(sprintf('Unknown placement "%s". Use one of [%s]', $placement, join(", ", $allowed)));
        }
    }
}
