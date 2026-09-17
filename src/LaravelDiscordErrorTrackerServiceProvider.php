<?php

namespace TomatoPHP\LaravelDiscordErrorTracker;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

class LaravelDiscordErrorTrackerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-discord-error-tracker.php', 'laravel-discord-error-tracker');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-discord-error-tracker.php' => config_path('laravel-discord-error-tracker.php'),
            ], 'laravel-discord-error-tracker-config');
        }

        // Laravel 11+ apps have no app/Exceptions/Handler, so hook into the bound handler directly.
        $this->callAfterResolving(ExceptionHandler::class, function (object $handler): void {
            if (config('laravel-discord-error-tracker.auto-report', true)) {
                DiscordServices::registerReportable($handler);
            }
        });
    }
}
