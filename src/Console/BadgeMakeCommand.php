<?php

namespace Terranet\Administrator\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class BadgeMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'administrator:badge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new administrator badge service.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Badge';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if (($t = $this->option('template')) && in_array($t, ['messages', 'notifications', 'tasks'], true)) {
            return __DIR__.'/stubs/badges/'.$t.'.stub';
        }

        return __DIR__.'/stubs/badges/messages.stub';
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
        return $rootNamespace.'\\'.config('administrator.paths.badge');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['template', 't', InputOption::VALUE_OPTIONAL, 'Use one of predefined templates.'],
        ];
    }
}
