<?php

namespace Terranet\Administrator\Services\Widgets;

use Coduo\PHPHumanizer\StringHumanizer;
use Terranet\Administrator\Contracts\Services\Widgetable;

class EloquentWidget extends AbstractWidget implements Widgetable
{
    /**
     * @var
     */
    private $eloquent;

    /**
     * EloquentWidget constructor.
     *
     * @param $eloquent
     */
    public function __construct($eloquent)
    {
        $this->eloquent = $eloquent;
    }

    /**
     * Widget contents.
     *
     * @return mixed
     */
    public function render()
    {
        $title = $this->fetchTitle();

        return view(app('scaffold.template')->view('model'), [
            'title' => $title,
            'item' => $this->eloquent,
        ]);
    }

    /**
     * @return string
     */
    protected function fetchTitle()
    {
        return str_singular(
            StringHumanizer::humanize(class_basename($this->eloquent))
        );
    }
}
