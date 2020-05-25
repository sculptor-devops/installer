<?php namespace App\Providers;

use Sculptor\Contracts\Runner;
use Sculptor\Runner\Runner as RunService;

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
