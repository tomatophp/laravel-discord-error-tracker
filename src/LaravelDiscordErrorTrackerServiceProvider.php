<?php

namespace TomatoPHP\LaravelDiscordErrorTracker;

use Illuminate\Support\ServiceProvider;

class LaravelDiscordErrorTrackerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Config file
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-discord-error-tracker.php', 'laravel-discord-error-tracker');

        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/laravel-discord-error-tracker.php' => config_path('laravel-discord-error-tracker.php'),
        ], 'laravel-discord-error-tracker-config');
    }

    public function boot(): void
    {
        // you boot methods here
    }
}
