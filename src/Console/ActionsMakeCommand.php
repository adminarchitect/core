<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;

class ActionsMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'administrator:actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator actions container.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Actions Container';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/actions.stub';
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
        return $rootNamespace.'\\'.config('administrator.paths.action');
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
