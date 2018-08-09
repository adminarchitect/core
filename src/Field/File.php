<?php

namespace Terranet\Administrator\Field;

use Illuminate\Support\Facades\View;
use Terranet\Administrator\Scaffolding;

class File extends Generic
{
    protected $visibility = [
        Scaffolding::PAGE_INDEX => true,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    /**
     * @return null|mixed|string
     */
    protected function onIndex()
    {
        return [
            'attachment' => $this->model->{$this->id},
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function onEdit()
    {
        return $this->onIndex();
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function onView()
    {
        return $this->onIndex();
    }
}
