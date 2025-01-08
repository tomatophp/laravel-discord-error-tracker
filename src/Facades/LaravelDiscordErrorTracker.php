<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void handler($exceptions)
 */
class LaravelDiscordErrorTracker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-discord-error-tracker';
    }
}
