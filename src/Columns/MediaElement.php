<?php

namespace Terranet\Administrator\Columns;

use Illuminate\Database\Eloquent\Model;

class MediaElement extends Element
{
    protected $conversion = '';

    protected $width = 300;

    protected $arrows = false;

    protected $indicators = true;

    protected $interval = 0;

    /**
     * Show/Hide Carousel arrows.
     *
     * @param bool $flag
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
     * @return $this]
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
     * @return $this
     */
    public function maxWidth($width)
    {
        $this->width = (int) $width;

        return $this;
    }

    public function render(Model $model)
    {
        return view('administrator::index.media', [
            'id' => $this->id,
            'media' => $model->getMedia($this->id),
            'conversion' => $this->conversion,
            'width' => $this->width,
            'hasArrows' => $this->arrows,
            'hasIndicators' => $this->indicators,
            'interval' => $this->interval,
        ]);
    }
}