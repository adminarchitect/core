<?php

namespace Terranet\Administrator\Traits;

use Illuminate\Translation\Translator;

trait AutoTranslatesInstances
{
    /** @var Translator */
    protected $translator;

    /**
     * @return Translator
     */
    public function translator()
    {
        if (null === $this->translator) {
            $this->translator = app('translator');
        }

        return $this->translator;
    }

    /**
     * @param $translator
     * @return $this
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    protected function translatableModule()
    {
        return app('scaffold.module');
    }
}
