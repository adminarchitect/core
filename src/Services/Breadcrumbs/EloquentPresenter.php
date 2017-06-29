<?php

namespace Terranet\Administrator\Services\Breadcrumbs;

use Illuminate\Database\Eloquent\Model;

class EloquentPresenter
{
    /**
     * @var Model
     */
    protected $eloquent;

    public function __construct($eloquent)
    {
        $this->eloquent = $eloquent;
    }

    public function present()
    {
        if (!($field = $this->getQualifiedTitleName())) {
            $field = $this->eloquent->getRouteKeyName();
        }

        return (string) $this->eloquent->getAttribute($field);
    }

    protected function getQualifiedTitleName()
    {
        foreach (['title', 'name', 'username', 'nickname'] as $column) {
            if (array_key_exists($column, $this->eloquent->toArray())) {
                return $column;
            }
        }

        return null;
    }
}
