<?php

namespace Terranet\Administrator\Services\Widgets;

use Coduo\PHPHumanizer\StringHumanizer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Terranet\Administrator\Contracts\Services\Widgetable;

class OneToOneRelation extends AbstractWidget implements Widgetable
{
    /**
     * @var Relation
     */
    protected $relation;

    public function __construct($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return Relation mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Widget contents.
     *
     * @return mixed
     */
    public function render()
    {
        $title = $this->fetchTitle();

        if ($related = $this->relation->getResults()) {
            return view(app('scaffold.template')->view('relations.one_to_one'), [
                'title' => $title,
                'related' => $related,
            ]);
        }

        return null;
    }

    /**
     * @return string
     */
    protected function fetchTitle()
    {
        return str_singular(
            StringHumanizer::humanize(class_basename($this->relation->getRelated()))
        );
    }
}
