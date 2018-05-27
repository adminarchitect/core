<?php

namespace Terranet\Administrator\Columns;

use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Traits\HasMediaOptions;

class MediaElement extends Element
{
    use HasMediaOptions;

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
