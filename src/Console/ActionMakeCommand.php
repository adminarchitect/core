<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ActionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'administrator:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator custom action.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('batch')) {
            return __DIR__.'/stubs/action.batch.stub';
        }

        return __DIR__.'/stubs/action.single.stub';
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
        return $rootNamespace.'\\'.config('administrator.paths.action_handler');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['batch', null, InputOption::VALUE_NONE, 'Generate an batch action handler.'],
        ];
    }
}
