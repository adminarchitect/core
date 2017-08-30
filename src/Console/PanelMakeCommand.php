<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class PanelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'administrator:panel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator dashboard panel.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Dashboard';

    public function handle()
    {
        parent::handle();

        if (!$this->option('no-view')) {
            $name = class_basename($this->qualifyClass($this->getNameInput()));

            $view = $this->templatePath($this->getViewName());

            $this->makeDirectory($view);

            $this->files->put($view, $this->templateContents(
                $name, ltrim(str_replace(base_path(), '', $view), DIRECTORY_SEPARATOR)
            ));
        }
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $view = $this->getViewName();

        $stub = str_replace('DummyClass', $class, $stub);

        return str_replace('DummyTemplate', 'admin.dashboard.' . $view, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('no-view')) {
            return __DIR__ . '/stubs/dashboard.panel.simple.stub';
        }

        return __DIR__ . '/stubs/dashboard.panel.stub';
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
        return $rootNamespace . '\\' . config('administrator.paths.panel');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['no-view', null, InputOption::VALUE_NONE, 'Do not generate view template for this panel.'],
        ];
    }

    private function templateContents($title, $path)
    {
        return <<<OUT
<div class="panel">
    <h3 class="panel-heading">{$title}</h3>
    <div class="panel-body">
        <p class="well">Check me out here [{$path}]</p>
    </div>
</div>
OUT;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function templatePath($name)
    {
        $tpl = base_path('resources/views/admin/dashboard/' . $name . '.blade.php');

        return $tpl;
    }

    /**
     * @return string
     */
    protected function getViewName()
    {
        return snake_case(class_basename($this->qualifyClass($this->getNameInput())));
    }
}
