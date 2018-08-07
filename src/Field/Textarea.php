<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class Textarea extends Generic
{
    /** @var array */
    protected $visibility = [
        Scaffolding::PAGE_INDEX => false,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    /**
     * @param string $page
     * @return mixed|string
     */
    public function render(string $page = 'index')
    {
        if (Scaffolding::PAGE_VIEW === $page) {
            return
                '<a href="#" onclick="[$(this), $(this).next()].forEach(e => {e.toggleClass(\'hidden\')}); return false;">'.
                '    <strong>'.trans('administrator::buttons.view_more').'</strong>'.
                '</a>'.
                '<div class="hidden">'.nl2br($this->value()).'</div>';
        }
    }
}
