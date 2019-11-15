<?php

namespace LGrevelink\CustomQueryBuilder\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeQueryBuilder extends GeneratorCommand
{
    private const DEFAULT_NAMESPACE = '\\QueryBuilders';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:query-builder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom query builder class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'CustomQueryBuilder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return realpath(__DIR__ . '/Stubs/make-query-builder.stub');
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
        return $rootNamespace . self::DEFAULT_NAMESPACE;
    }
}
