<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;

class SaverMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'administrator:saver {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator module saver service.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Saver';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/saver.stub';
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
        return $rootNamespace.'\\'.config('administrator.paths.saver');
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
