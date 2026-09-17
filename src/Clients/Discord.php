<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Clients;

use Illuminate\Support\Facades\Http;
use Throwable;
use TomatoPHP\LaravelDiscordErrorTracker\Exceptions\DiscordWebhookException;

class Discord
{
    /**
     * Post a payload to a Discord webhook.
     *
     * @param  array<string, mixed>  $params
     *
     * @throws DiscordWebhookException
     */
    public static function send(array $params, ?string $webhook = null): void
    {
        $webhook = $webhook ?: config('laravel-discord-error-tracker.error-webhook');

        if (blank($webhook)) {
            throw new DiscordWebhookException('The Discord error webhook URL is not configured.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('laravel-discord-error-tracker.timeout', 10))
                ->post($webhook, $params);
        } catch (Throwable $exception) {
            throw new DiscordWebhookException('Discord webhook request failed: ' . $exception->getMessage(), 0, $exception);
        }

        if ($response->failed()) {
            throw new DiscordWebhookException('Discord webhook responded with HTTP ' . $response->status() . ': ' . $response->body());
        }
    }
}
