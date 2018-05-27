<?php

namespace Terranet\Administrator\Contracts;

interface AutoTranslatable
{
    /**
     * Set a translator object.
     *
     * @param $translator
     *
     * @return mixed
     */
    public function setTranslator($translator);

    /**
     * Returns a translator object.
     *
     * @return mixed
     */
    public function translator();

    /**
     * Builds a translation key.
     *
     * @return string
     */
    public function translationKey();
}
