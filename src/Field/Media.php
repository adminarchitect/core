<?php

namespace Terranet\Administrator\Field;

use Spatie\MediaLibrary\Models\Media as MediaModel;
use Terranet\Administrator\Architect;

class Media extends Field
{
    /** @var string */
    protected $conversion = '';

    /**
     * @param string $conversion
     *
     * @return self
     */
    public function convertedTo(string $conversion): self
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        $media = $this->model->getMedia($this->id());
        $module = Architect::resourceByEntity($this->model) ?: app('scaffold.module');

        return [
            'media' => $media,
            'module' => $module,
        ];
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        $media = $this->model->getMedia($this->id());
        $media = $media->map(function ($item) {
            return array_merge($item->toArray(), [
                'url' => $item->getUrl(),
                'conversions' => $this->conversions($item),
            ]);
        });

        return [
            'media' => $media,
            'conversion' => $this->conversion,
        ];
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        return $this->onView();
    }

    /**
     * @param MediaModel $item
     *
     * @return array
     */
    protected function conversions(MediaModel $item)
    {
        return array_build($item->getMediaConversionNames(), function ($key, $conversion) use ($item) {
            return [$conversion, $item->getUrl($conversion)];
        });
    }
}
