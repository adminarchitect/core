<?php

namespace Terranet\Administrator\Traits;

use Illuminate\Translation\Translator;

trait AutoTranslatesInstances
{
    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    protected function translatableModule()
    {
        return app('scaffold.module');
    }
}
