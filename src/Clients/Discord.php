<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Clients;

use Illuminate\Support\Facades\Http;

class Discord
{
    /**
     * @param ?string $webhook
     * @param array $params
     * @return void
     */
    public static function send(array $params, ?string $webhook=null): void
    {
        Http::post($webhook ?: config('laravel-discord-error-tracker.webhook'), $params)->json();
    }
}
