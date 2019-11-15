<?php

namespace LGrevelink\CustomQueryBuilder;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LGrevelink\CustomQueryBuilder\Console\Commands\MakeQueryBuilder;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Boot any Custom Query Builder related services.
     */
    public function boot()
    {
        // Only register the commands when running from the console.
        if ($this->app->runningInConsole()) {
            $this->addCommands();
        }
    }

    /**
     * Adds the package's commands to artisan.
     */
    protected function addCommands()
    {
        $this->commands([
            MakeQueryBuilder::class,
        ]);
    }
}
