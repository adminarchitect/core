<?php

namespace Terranet\Administrator\Form\Type;

use Coduo\PHPHumanizer\StringHumanizer;
use Terranet\Administrator\Exception;

class Image extends File
{
    static protected $hrefStyle = 'display: inline-block; text-align: center;';
    static protected $blockStyle = 'display:inline-block; margin-right: 5px; border: 1px solid gray;';

    protected $thumbSize = 75;

    /**
     * @return array|string
     */
    protected function listFiles()
    {
        $files = [];

        $styles = $this->value()->getConfig()->styles;

        foreach ($styles as $style) {
            list($w, $h) = $this->getThumbnailSize($style);

            $img =
                '<a href="' . $this->value()->url($style->name) . '" class="fancybox" style="' . static::$hrefStyle . '">' .
                '   <img src="' . $this->value()->url($style->name) . '" style="width: ' . ($w ?: 'auto') . 'px; height: ' . ($h ?: 'auto') . 'px;" />' .
                '   <div>' .
                '   ' . StringHumanizer::humanize($style->name) .
                '   </div>' .
                '</a>';

            $files[$style->name] = $img;
        }

        $columnStart = '<div style="' . static::$blockStyle . '">';
        $columnEnd = '</div>';

        return
            '<div>' .
            '   ' . $columnStart . implode($columnEnd . $columnStart, $files) . $columnEnd .
            '</div>';
    }

    protected function isOriginal($style)
    {
        return 'original' == $style->name;
    }

    protected function dimensions($style)
    {
        if ($style->dimensions && str_contains('x', $style->dimensions)) {
            return array_map('intval', explode('x', $style->dimensions));
        }

        if (!$size = @getimagesize($this->value()->path($style->name))) {
            return [1, 1];
        }

        return array_map('intval', [$size[0], $size[1]]);
    }

    /**
     * @param $style
     * @return array
     */
    protected function getThumbnailSize($style)
    {
        list($w, $h) = $this->dimensions($style);

        $ratio = $this->thumbSize / min(($w ?: $h), ($h ?: $w));

        $w = (int) round($w * $ratio, 0);
        $h = (int) round($h * $ratio, 0);

        return [$w, $h];
    }

    /**
     * Set thumbnail size.
     *
     * @param int $thumbSize
     * @return $this
     */
    public function setThumbSize($thumbSize)
    {
        $this->thumbSize = $thumbSize;

        return $this;
    }
}
