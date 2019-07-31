<?php

namespace Terranet\Administrator\Traits\Module;

use Terranet\Administrator\Decorators\Grid;
use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Translatable\Translatable;

trait HasForm
{
    /**
     * Provides array of editable columns.
     *
     * @return Mutable
     */
    public function form()
    {
        return $this->scaffoldForm();
    }

    /**
     * Build editable columns based on table columns metadata.
     *
     * @return Mutable
     */
    protected function scaffoldForm()
    {
        $editable = new Mutable();

        if ($eloquent = $this->model()) {
            $editable = $editable
                ->merge([$eloquent->getKeyName()])
                ->merge($translatable = $this->scaffoldTranslatable($eloquent))
                ->merge($eloquent->getFillable());

            return $editable->build(
                new Grid($eloquent)
            )->map(function ($element) use ($translatable) {
                if (\in_array($element->id(), $translatable, true)) {
                    return $element->translatable();
                }

                return $element;
            });
        }

        return $editable;
    }

    /**
     * @param $eloquent
     * @return array
     */
    protected function scaffoldTranslatable($eloquent)
    {
        return ($eloquent instanceof Translatable && method_exists($eloquent, 'getTranslatedAttributes'))
            ? $eloquent->getTranslatedAttributes()
            : [];
    }
}
