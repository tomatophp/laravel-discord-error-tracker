<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use TomatoPHP\LaravelDiscordErrorTracker\LaravelDiscordErrorTrackerServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDiscordErrorTrackerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('laravel-discord-error-tracker.error-webhook-active', true);
        $app['config']->set('laravel-discord-error-tracker.error-webhook', 'https://discord.com/api/webhooks/1263430024275562577/xEPrYTn6IsDRvfyFpoAIK-Avmdr56BLXw5RxHxDa4E7FJ8p_4bzESh0nep6XSWk9z1V5');
    }
}
