<?php

namespace Eighteen73\Radioactivity;

use Eighteen73\Radioactivity\Jobs\EnergyDecay;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;

class RadioactivityServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('radioactivity', function () {
            return new Radioactivity;
        });
    }

    public function boot()
    {
        $this->registerMigrations();
        $this->registerSchedule();

        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], 'radioactivity_migrations');

        $this->publishes([
            __DIR__.'/../config/radioactivity.php' => config_path('radioactivity.php'),
        ]);
    }

    public function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    public function registerSchedule()
    {
        Schedule::job(new EnergyDecay)->cron('*/'.EnergyDecay::RUN_EVERY.' * * * *');
    }
}
