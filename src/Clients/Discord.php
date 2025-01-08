<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Clients;

use Illuminate\Support\Facades\Http;

class Discord
{
    public static function send(array $params, ?string $webhook = null): void
    {
        Http::post($webhook ?: config('laravel-discord-error-tracker.webhook'), $params)->json();
    }
}
