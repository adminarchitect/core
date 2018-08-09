<?php

namespace Terranet\Administrator\Form\Type;

use Coduo\PHPHumanizer\StringHumanizer;

class Image extends File
{
    protected static $hrefStyle = 'display: inline-block; text-align: center;';
    protected static $blockStyle = 'display:inline-block; margin-right: 5px; border: 1px solid gray;';

    protected $thumbSize = 75;

    /**
     * Set thumbnail size.
     *
     * @param int $thumbSize
     *
     * @return $this
     */
    public function setThumbSize($thumbSize)
    {
        $this->thumbSize = $thumbSize;

        return $this;
    }

    /**
     * @return array|string
     */
    protected function listFiles()
    {
        $files = [];

        if (!array_has($variants = $this->value()->getConfig()['variants'], 'original')) {
            $variants['original'] = 'original';
        }
        foreach ($variants as $name => $style) {
            [$w, $h] = $this->getThumbnailSize($style, $name);

            $img =
                '<a rel="'.str_slug($this->getFormName()).'" href="'.$this->value()->url($name).'" class="fancybox" style="'.static::$hrefStyle.'">'.
                '   <img src="'.$this->value()->url($name).'" style="width: '.($w ? "{$w}px" : 'auto').'; height: '.($h ? "{$h}px" : 'auto').'" />'.
                '   <div>'.StringHumanizer::humanize($name).'</div>'.
                '</a>';

            $files[$name] = $img;
        }

        $columnStart = '<div style="'.static::$blockStyle.'">';
        $columnEnd = '</div>';

        return
            '<div>'.
            '   '.$columnStart.implode($columnEnd.$columnStart, $files).$columnEnd.
            '</div>';
    }

    protected function isOriginal($style)
    {
        return 'original' === $style->name;
    }

    protected function dimensions($style, $name)
    {
        $dimensions = array_get($style, 'resize.dimensions');

        if ($dimensions && str_contains('x', $dimensions)) {
            return array_map('intval', explode('x', $dimensions));
        }

        if (!$size = @getimagesize($this->value()->path($name))) {
            return [1, 1];
        }

        return array_map('intval', [$size[0], $size[1]]);
    }

    /**
     * @param $style
     * @param $name
     *
     * @return array
     */
    protected function getThumbnailSize($style, $name)
    {
        [$w, $h] = $this->dimensions($style, $name);

        $ratio = $this->thumbSize / min(($w ?: $h), ($h ?: $w));

        $w = (int) round($w * $ratio, 0);
        $h = (int) round($h * $ratio, 0);

        return [$w, $h];
    }
}
