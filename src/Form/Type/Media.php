<?php

namespace Terranet\Administrator\Form\Type;

use Terranet\Administrator\Form\Element;
use Terranet\Administrator\Traits\HasMediaOptions;

class Media extends Element
{
    use HasMediaOptions;

    /**
     * Each subclass should have this method realized.
     *
     * @return mixed
     */
    public function render()
    {
        $media = $this->getRepository()->getMedia(
            $name = $this->getFormName()
        )->map(function ($item) {
            return array_merge($item->toArray(), [
                'url' => $item->getUrl(),
                'conversions' => $this->conversions($item),
            ]);
        });

        return view('administrator::edit.controls.media', [
            'name' => $name,
            'media' => $media->toJson(),
            'arrows' => $this->arrows,
            'indicators' => $this->indicators,
            'width' => $this->width,
            'conversion' => $this->conversion,
            'editable' => $this->editable,
        ]);
    }

    protected function conversions(\Spatie\MediaLibrary\Models\Media $item)
    {
        return array_build($item->getMediaConversionNames(), function ($key, $conversion) use ($item) {
            return [$conversion, $item->getUrl($conversion)];
        });
    }
}
