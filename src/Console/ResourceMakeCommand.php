<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;

class ResourceMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'administrator:resource {name} {model?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator resource.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Module';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/module.stub';
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
        return $rootNamespace.'\Http\Terranet\Administrator\Modules';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if ($model = $this->argument('model')) {
            if (!starts_with($model, $namespace = $this->laravel->getNamespace())) {
                $model = "{$namespace}\\{$model}";
            }
        }

        return $this->replaceModel($stub, $model);
    }

    /**
     * Replace the model name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceModel($stub, $name)
    {
        return str_replace('DummyModel', $name ?: '\App\User', $stub);
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
