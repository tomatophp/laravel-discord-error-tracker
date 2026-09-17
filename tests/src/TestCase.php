<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TomatoPHP\LaravelDiscordErrorTracker\LaravelDiscordErrorTrackerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public const WEBHOOK = 'https://discord.test/api/webhooks/123/token';

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDiscordErrorTrackerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('laravel-discord-error-tracker.error-webhook-active', true);
        $app['config']->set('laravel-discord-error-tracker.error-webhook', self::WEBHOOK);
    }
}
