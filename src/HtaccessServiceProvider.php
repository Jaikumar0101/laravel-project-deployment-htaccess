<?php

namespace Jaikumar0101\LaravelHtaccess;

use Illuminate\Support\ServiceProvider;
use Jaikumar0101\LaravelHtaccess\Console\InstallHtaccessCommand;

class HtaccessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallHtaccessCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
