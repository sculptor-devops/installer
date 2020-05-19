<?php

namespace App\Providers;

use Eppak\Contracts\Runner;
use Eppak\Runner\Runner as RunService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(Runner::class, function() {
            return new RunService();
        });
    }
}
