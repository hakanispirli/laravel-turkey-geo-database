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

            // Publish migrations with dynamic timestamps
            $this->publishes([
                __DIR__ . '/Database/Migrations/create_cities_table.php.stub' => $this->getMigrationFileName('create_cities_table.php'),
                __DIR__ . '/Database/Migrations/create_districts_table.php.stub' => $this->getMigrationFileName('create_districts_table.php'),
                __DIR__ . '/Database/Migrations/create_neighborhoods_table.php.stub' => $this->getMigrationFileName('create_neighborhoods_table.php'),
            ], 'turkey-geo-migrations');

            // Publish data files
            $this->publishes([
                __DIR__ . '/data' => database_path('data/turkey-geo'),
            ], 'turkey-geo-data');
        }
    }

    /**
     * Returns existing migration file name if found, else uses current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make('files');

        return database_path('migrations/' . $timestamp . '_' . $migrationFileName);
    }
}
