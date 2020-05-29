<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Runner\Runner as RunnerImplementation;
use Sculptor\Foundation\Database\MySql;

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
            return new RunnerImplementation();
        });

        app()->bind(Database::class, function() {
            return new MySql();
        });
    }
}
