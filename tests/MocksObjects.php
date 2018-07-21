<?php

namespace Terranet\Administrator\Tests;

trait MocksObjects
{
    /**
     * @return \Mockery\MockInterface
     */
    protected function mockTranslator()
    {
        $translator = \Mockery::mock(\Illuminate\Translation\Translator::class);
        $translator->shouldReceive('has')->withAnyArgs()->andReturn(false);

        app()->instance('translator', $translator);

        return $translator;
    }

    protected function mockModule()
    {
        $module = \Mockery::mock(\Terranet\Administrator\Scaffolding::class);
        $module->shouldReceive('url')->andReturn('/module/url');

        app()->instance('scaffold.module', $module);

        return $module;
    }

    /**
     * @param $translator
     */
    protected function mockApplication($translator)
    {
        $app = \Mockery::mock(\Illuminate\Contracts\Foundation\Application::class);
        $app->shouldReceive('make')
            ->with('translator')
            ->andReturn($translator);
    }
}
