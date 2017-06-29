<?php

namespace Terranet\Administrator\Services\Widgets;

use Coduo\PHPHumanizer\StringHumanizer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Terranet\Administrator\Contracts\Services\Widgetable;

class OneToManyRelation extends AbstractWidget implements Widgetable
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
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Widget contents
     *
     * @return mixed
     */
    public function render()
    {
        $title = $this->fetchTitle();

        $collection = $this->relation->getResults();

        return view(app('scaffold.template')->view('relations.one_to_many'), [
            'title'      => $title,
            'collection' => $collection
        ]);
    }

    /**
     * @return string
     */
    protected function fetchTitle()
    {
        $source = $this->relation->getRelated();

        return str_plural(
            StringHumanizer::humanize(class_basename($source))
        );
    }
}
