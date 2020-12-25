<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class File extends Field
{
    public $visibility = [
        Scaffolding::PAGE_INDEX => true,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    public function onEdit(): array
    {
        return $this->onIndex();
    }

    public function onView(): array
    {
        return $this->onIndex();
    }

    protected function onIndex(): array
    {
        return [
            'attachment' => $this->model ? $this->model->{$this->id} : null,
        ];
    }
}
