![Screenshot](https://raw.githubusercontent.com/tomatophp/laravel-discord-error-tracker/master/arts/screenshot.jpg)

# Laravel Discord Error Tracker

[![Dependabot Updates](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/tests.yml/badge.svg)](https://github.com/tomatophp/laravel-discord-error-tracker/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/version.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![License](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/license.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![Downloads](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/d/total.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)

Track Your Errors using Discord Webhook on any Laravel App

## Compatibility

| Version | Laravel      | PHP   | Branch   |
|---------|--------------|-------|----------|
| 2.x     | 12.x, 13.x   | 8.2+  | `master` |
| 1.x     | 10.x, 11.x   | 8.1+  | `v1`     |

## Installation

```bash
composer require tomatophp/laravel-discord-error-tracker
```

Add these to your `.env`:

```dotenv
DISCORD_ERROR_WEBHOOK_ACTIVE=true   # turn Discord reporting on (default: false)
DISCORD_ERROR_WEBHOOK=https://discord.com/api/webhooks/{id}/{token}   # your channel webhook URL
DISCORD_ERROR_EVERYONE=true         # mention @everyone on each error (default: true)
DISCORD_ERROR_AUTO_REPORT=true      # report every exception automatically (default: true)
DISCORD_ERROR_TRACE_LIMIT=4000      # max trace characters in the message (default: 4000, capped at 4090)
DISCORD_ERROR_TIMEOUT=10            # webhook request timeout in seconds (default: 10)
```

Nothing is sent while `DISCORD_ERROR_WEBHOOK_ACTIVE` is false or `DISCORD_ERROR_WEBHOOK` is empty.

## How reporting works

Laravel 11+ apps have no `app/Exceptions/Handler.php`. The package's service provider (auto-discovered) registers a
`reportable` callback on the application's exception handler, so every exception Laravel reports (the ones that pass
`dontReport` / `$exceptions->dontReport()`) is sent to Discord. No change to `bootstrap/app.php` is needed, and Laravel's
own logging keeps running.

The message is sent by the queued `NotifyDiscordJob` on your default queue connection (3 tries, 10 seconds backoff).
With `QUEUE_CONNECTION=sync` it is sent during the request; otherwise run a queue worker.

A failing or unreachable webhook never breaks your app, and webhook failures (`DiscordWebhookException`) are never
reported back to Discord, so there is no report loop.

If you prefer wiring it by hand, set `DISCORD_ERROR_AUTO_REPORT=false` and add it in `bootstrap/app.php`
(calling it with auto-report on is harmless: the callback is only registered once):

```php
use Illuminate\Foundation\Configuration\Exceptions;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

->withExceptions(function (Exceptions $exceptions) {
    DiscordServices::handler($exceptions);
})
```

To report a single exception yourself:

```php
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

DiscordServices::handler(new \Exception('Test Exception'));
```

The payload respects Discord limits (title 256, description 4096, 25 fields, field value 1024, 6000 characters per
embed); long stack traces are truncated.

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-config"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Testing

if you like to run `PEST` testing just use this command

```bash
composer test
```

## Code Style

if you like to fix the code style just use this command

```bash
composer format
```

## PHPStan

if you like to check the code by `PHPStan` just use this command

```bash
composer analyse
```

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
