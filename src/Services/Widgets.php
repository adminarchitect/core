<?php

namespace Terranet\Administrator\Services;

use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Contracts\Services\Widgetable;
use Terranet\Administrator\Services\Widgets\AbstractWidget;
use Terranet\Administrator\Services\Widgets\EloquentWidget;

class Widgets
{
    /**
     * @var array
     */
    protected $widgets;

    protected $tab;

    protected $placement;

    /**
     * Widget constructor.
     *
     * @param Mutable $widgets
     */
    public function __construct(Mutable $widgets = null)
    {
        $this->widgets = $widgets;
    }

    public function setTab($tab)
    {
        $this->tab = $tab;

        return $this;
    }

    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    public function add(Widgetable $widget)
    {
        $this->widgets->push($widget);

        return $this;
    }

    /**
     * Fetch widgets
     *
     * @return array
     */
    public function filter()
    {
        $widgets = $this->applyFilters();

        return $widgets->sortBy(function (AbstractWidget $w1) {
            return $w1->getOrder();
        });
    }

    protected function applyFilters()
    {
        return $this->widgets->filter(function (AbstractWidget $widget) {
            return ($widget->getPlacement() === $this->placement && $widget->getTab() === $this->tab);
        });
    }

    public function tabs()
    {
        // eloquent tab should be always first
        $this->sortTabs();

        return array_build($this->widgets, function ($i, $widget) {
            return [str_slug($tab = $widget->getTab()), $tab];
        });
    }

    private function sortTabs()
    {
        $this->widgets = $this->widgets->sort(function (AbstractWidget $a, AbstractWidget $b) {
            if ($a instanceof EloquentWidget) {
                return 1;
            }

            if ($b instanceof EloquentWidget) {
                return 1;
            }

            return $a->getTab() < $b->getTab() ? -1 : 1;
        });

        return $this->widgets;
    }
}
