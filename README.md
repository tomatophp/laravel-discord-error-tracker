![Screenshot](https://raw.githubusercontent.com/tomatophp/laravel-discord-error-tracker/master/art/screenshot.jpg)

# Laravel Discord Error Tracker

[![Latest Stable Version](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/version.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![License](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/license.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![Downloads](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/d/total.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)

Track Your Errors using Discord Webhook on any Laravel App

## Installation

```bash
composer require tomatophp/laravel-discord-error-tracker
```

on your env

```dotenv
DISCORD_ERROR_WEBHOOK_ACTIVE=true #Enable And Disable Log
DISCORD_ERROR_EVERYONE=true #Enable And Disable @everyone alert on the message
DISCORD_ERROR_WEBHOOK= #Your Discord Server Channel Webhook URL
```

## Using

if you are using Laravel 11 or above use this code, at `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use TomatoPHP\LaravelDiscordErrorTracker\Facades\LaravelDiscordErrorTracker;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        LaravelDiscordErrorTracker::handler($exceptions);
    })->create();
```

if you are using Laravel 10, use this code, at `app\Exceptions\Handler.php`

````php
<?php

namespace App\Exceptions;

use TomatoPHP\LaravelDiscordErrorTracker\Facades\LaravelDiscordErrorTracker;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $exception) {
            LaravelDiscordErrorTracker::handler($exception);
        });
    }
}
```

this way will log all errors on your app, if you like to log selected error you can use this method direct

```php
<?php

use TomatoPHP\LaravelDiscordErrorTracker\Facades\LaravelDiscordErrorTracker;

$exception = new \Exception('Test Exception');
LaravelDiscordErrorTracker::handler($exception);
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-config"
````

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
