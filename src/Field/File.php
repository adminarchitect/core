<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class File extends Generic
{
    protected $visibility = [
        Scaffolding::PAGE_INDEX => true,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    /**
     * @return array
     */
    public function onEdit(): array
    {
        return $this->onIndex();
    }

    /**
     * @return array
     */
    public function onView(): array
    {
        return $this->onIndex();
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        return [
            'attachment' => $this->model ? $this->model->{$this->id} : null,
        ];
    }
}
