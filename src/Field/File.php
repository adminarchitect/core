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
     * @param string $page
     *
     * @return null|mixed|string
     */
    public function render(string $page = 'index')
    {
        return \admin\output\staplerImage($this->model->{$this->id}, [], []);
    }
}
