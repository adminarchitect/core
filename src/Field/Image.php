<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class Image extends Generic
{
    /** @var string */
    protected $style = 'original';

    /** @var bool */
    protected $rounded = true;

    /** @var array */
    protected $attributes = [
        'width' => 75,
        'height' => 75,
    ];

    /**
     * @param $style
     *
     * @return $this
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @param int $width
     * @param null|int $height
     */
    public function setSize(int $width, int $height = null)
    {
        if (!$height) {
            $height = $width;
        }

        $this->attributes = array_merge($this->attributes(), [
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * @param bool $rounded
     *
     * @return $this
     */
    public function square()
    {
        $this->rounded = false;

        return $this;
    }

    /**
     * @param string $page
     *
     * @return null|mixed|string
     */
    public function render(string $page = 'index')
    {
        if (Scaffolding::PAGE_INDEX === $page) {
            return \admin\output\staplerImage($this->model->{$this->id}, $this->style, $this->attributes());
        }

        if (Scaffolding::PAGE_VIEW === $page) {
            $this->rounded = false;
            $this->setSize(480);

            return \admin\output\staplerImage($this->model->{$this->id}, $this->style, $this->attributes());
        }
    }

    /**
     * @return array
     */
    protected function attributes()
    {
        if (!array_key_exists('id', $this->attributes)) {
            $this->attributes['id'] = $this->id();
        }

        $this->attributes['class'] = $this->rounded
            ? 'img-circle'
            : 'img-responsive';

        return $this->attributes;
    }
}
