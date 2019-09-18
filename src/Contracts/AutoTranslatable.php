<?php

namespace Terranet\Administrator\Contracts;

interface AutoTranslatable
{
    /**
     * Builds a translation key.
     *
     * @return string
     */
    public function translationKey();
}
