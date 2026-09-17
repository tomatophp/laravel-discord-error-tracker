<?php

use Illuminate\Support\ServiceProvider;
use TomatoPHP\LaravelDiscordErrorTracker\LaravelDiscordErrorTrackerServiceProvider;

it('boots the service provider', function () {
    expect(app()->getProvider(LaravelDiscordErrorTrackerServiceProvider::class))
        ->toBeInstanceOf(LaravelDiscordErrorTrackerServiceProvider::class);
});

it('merges the package config', function () {
    expect(config('laravel-discord-error-tracker'))
        ->toHaveKeys(['error-webhook-active', 'error-webhook', 'everyone', 'auto-report', 'trace-limit', 'timeout'])
        ->and(config('laravel-discord-error-tracker.auto-report'))->toBeTrue()
        ->and(config('laravel-discord-error-tracker.trace-limit'))->toBe(4000);
});

it('registers the config file for publishing', function () {
    $paths = ServiceProvider::pathsToPublish(LaravelDiscordErrorTrackerServiceProvider::class, 'laravel-discord-error-tracker-config');

    expect($paths)->toHaveCount(1)
        ->and(array_key_first($paths))->toEndWith('laravel-discord-error-tracker.php')
        ->and(file_exists(array_key_first($paths)))->toBeTrue()
        ->and(array_values($paths)[0])->toBe(config_path('laravel-discord-error-tracker.php'));
});
