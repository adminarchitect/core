<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;

class BreadcrumbsMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'administrator:breadcrumbs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator module breadcrumbs handler.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Breadcrumbs';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/breadcrumbs.stub';
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
        return $rootNamespace.'\\'.config('administrator.paths.breadcrumbs');
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
