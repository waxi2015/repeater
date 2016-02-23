<?php

namespace Waxis\Repeater;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class RepeaterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
