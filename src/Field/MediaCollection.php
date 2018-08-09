<?php

namespace Terranet\Administrator\Field;

use Spatie\MediaLibrary\Models\Media;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\HasMediaOptions;

class MediaCollection extends Generic
{
    use HasMediaOptions;

    public function render(string $page = 'index')
    {
        $media = $this->model->getMedia(
            $name = $this->id()
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
            'editable' => Scaffolding::PAGE_EDIT === $page && $this->editable,
        ]);
    }

    /**
     * @param Media $item
     * @return array
     */
    protected function conversions(Media $item)
    {
        return array_build($item->getMediaConversionNames(), function ($key, $conversion) use ($item) {
            return [$conversion, $item->getUrl($conversion)];
        });
    }
}