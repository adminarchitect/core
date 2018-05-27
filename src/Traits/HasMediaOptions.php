<?php

namespace Terranet\Administrator\Traits;

trait HasMediaOptions
{
    protected $width = 300;

    protected $arrows = false;

    protected $interval = 0;

    protected $indicators = true;

    protected $conversion = '';

    protected $editable = true;

    /**
     * Show/Hide Carousel arrows.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function hasArrows(bool $flag)
    {
        $this->arrows = (bool) $flag;

        return $this;
    }

    /**
     * Show/Hide Carousel indicators.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function hasIndicators(bool $flag)
    {
        $this->indicators = (bool) $flag;

        return $this;
    }

    /**
     * Media conversion name.
     *
     * @param $conversion
     *
     * @return $this
     */
    public function convertedTo($conversion)
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * AutoPlay interval in minutes.
     *
     * @param $interval
     *
     * @return $this
     */
    public function autoPlay($interval)
    {
        $this->interval = (int) $interval * 1000;

        return $this;
    }

    /**
     * Carousel max Width.
     *
     * @param $width
     *
     * @return $this
     */
    public function maxWidth($width)
    {
        $this->width = (int) $width;

        return $this;
    }

    /**
     * Enable/Disable uploads.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function editable(bool $flag = true)
    {
        $this->editable = (bool) $flag;

        return $this;
    }
}
