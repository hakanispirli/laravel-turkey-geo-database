<?php

namespace Webmarka\TurkeyGeo;

use Illuminate\Support\ServiceProvider;

class TurkeyGeoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->offerPublishing();
        $this->registerMigrations();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/turkey-geo.php',
            'turkey-geo'
        );
    }

    /**
     * Setup the resource publishing groups for the package.
     */
    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/config/turkey-geo.php' => config_path('turkey-geo.php'),
            ], 'turkey-geo-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/Database/Migrations' => database_path('migrations'),
            ], 'turkey-geo-migrations');

            // Publish data files
            $this->publishes([
                __DIR__ . '/data' => database_path('data/turkey-geo'),
            ], 'turkey-geo-data');
        }
    }

    /**
     * Register the package migrations.
     */
    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        }
    }
}
