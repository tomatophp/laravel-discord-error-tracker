<?php

namespace TomatoPHP\LaravelDiscordErrorTracker;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

class LaravelDiscordErrorTrackerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register generate command
        $this->commands([
            \TomatoPHP\LaravelDiscordErrorTracker\Console\LaravelDiscordErrorTrackerInstall::class,
        ]);

        // Register Config file
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-discord-error-tracker.php', 'laravel-discord-error-tracker');

        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/laravel-discord-error-tracker.php' => config_path('laravel-discord-error-tracker.php'),
        ], 'laravel-discord-error-tracker-config');

        // Register Langs
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-discord-error-tracker');

        // Publish Lang
        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('lang/vendor/laravel-discord-error-tracker'),
        ], 'laravel-discord-error-tracker-lang');

        $this->app->bind('laravel-discord-error-tracker', function () {
            return new DiscordServices;
        });

    }

    public function boot(): void
    {
        // you boot methods here
    }
}
