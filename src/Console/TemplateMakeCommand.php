<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;

class TemplateMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'administrator:template {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator module templates handler.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Templates';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/template.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.config('administrator.paths.template');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
