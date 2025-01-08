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
            LaravelDiscordErrorTrackerServiceProvider::class
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__ . '/../resources/views',
        ]);
    }
}
