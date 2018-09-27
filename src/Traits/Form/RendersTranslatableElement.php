<?php

namespace Terranet\Administrator\Traits\Form;

use Terranet\Administrator\Scaffolding;
use Terranet\Translatable\Translatable;

trait RendersTranslatableElement
{
    /**
     * @return mixed
     */
    public function render($page = Scaffolding::PAGE_INDEX)
    {
        /**
         * to be able using translations we have to get element's Eloquent model.
         *
         * @var \Terranet\Translatable\Translatable;
         */
        $repository = app('scaffold.model') ?: app('scaffold.module')->model();

        $cycle = 0;
        $current = \localizer\locale();
        $translations = app('scaffold.translations');

        $inputs = array_build(
            \localizer\locales(),
            function ($key, $locale) use ($repository, $current, &$cycle, $translations) {
                $element = $this->selfClone($locale, $repository);

                if ($translations->readonly($locale)) {
                    $element->setAttributes(['disabled' => true]);
                }

                $input = $element->render();//.$element->errors();

                $input = view(
                    'administrator::partials.forms.translatable.element',
                    [
                        'element' => $element,
                        'locale' => $locale,
                        'current' => $current,
                        'input' => $input,
                    ]
                );

                return [$cycle++, $input];
            }
        );

        return view('administrator::partials.forms.translatable.container')
            ->with([
                'element' => $this,
                'inputs' => $inputs,
                'current' => \localizer\locale(),
            ]);
    }

    /**
     * @param $locale
     * @param $repository
     *
     * @return RendersTranslatableElement
     */
    protected function selfClone($locale, $repository)
    {
        $element = clone $this->field;

        $element->setName(
            $this->translatableName($locale)
        );

        // set translated value
        if ($repository instanceof Translatable && $repository->hasTranslation($locale->id())) {
            $element->setValue(
                $repository->translate($locale->id())->{$this->id()}
            );
        }

        return $element;
    }

    /**
     * @param $locale
     *
     * @return string
     */
    protected function translatableName($locale)
    {
        return "translatable[{$locale->id()}][{$this->id()}]";
    }
}
