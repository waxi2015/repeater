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
            require __DIR__.'/routes.php';
        }

        $this->publishes([
            __DIR__.'/Repeater/Template/custom.phtml' => resource_path('views/repeater/custom.phtml'),
            __DIR__.'/Descriptors/Example.php' => app_path('Descriptors/Repeater/Example.php'),
            __DIR__.'/assets' => resource_path('assets/common/libs/repeater/'),
            __DIR__.'/lang' => resource_path('lang'),
        ]);
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
