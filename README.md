![Screenshot](https://raw.githubusercontent.com/tomatophp/laravel-discord-error-tracker/master/art/screenshot.jpg)

# Laravel discord error tracker

[![Latest Stable Version](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/version.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![License](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/license.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)
[![Downloads](https://poser.pugx.org/tomatophp/laravel-discord-error-tracker/d/total.svg)](https://packagist.org/packages/tomatophp/laravel-discord-error-tracker)

Track Your Errors using Discord Webhook on any Laravel App

## Installation

```bash
composer require tomatophp/laravel-discord-error-tracker
```
after install your package please run this command

```bash
php artisan laravel-discord-error-tracker:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\LaravelDiscordErrorTracker\LaravelDiscordErrorTrackerPlugin::make())
```


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="laravel-discord-error-tracker-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
