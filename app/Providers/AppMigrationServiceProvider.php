<?php

namespace App\Providers;
use App\Console\Overrides\AppMigrationCreator;

use Illuminate\Support\ServiceProvider;

class AppMigrationServiceProvider extends ServiceProvider
{

    protected $defer = true;
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new AppMigrationCreator($app['files']);
        });
    }

    public function provides()
    {
        return ['migration.creator'];
    }

}
