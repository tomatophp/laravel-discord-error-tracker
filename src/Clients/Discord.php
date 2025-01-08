<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Clients;

use Illuminate\Support\Facades\Http;

class Discord
{
    public static function send(array $params, ?string $webhook = null): void
    {
        $response = Http::post($webhook ?? config('laravel-discord-error-tracker.error-webhook'), $params);

        if ($response->failed()) {
            throw new \Exception($response->body());
        }
    }
}
