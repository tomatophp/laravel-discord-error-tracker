<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services;

use Throwable;
use TomatoPHP\FilamentDiscordDriver\Jobs\NotifyDiscordJob;
use TomatoPHP\LaravelDiscordErrorTracker\Clients\Discord;
use Illuminate\Foundation\Configuration\Exceptions;
class DiscordServices
{
    private ?string $webhook;

    private ?string $title;

    private ?string $message;

    private ?string $url;

    private ?string $image;

    private function send(): void
    {
        $embeds = [];
        if ($this->message) {
            $embeds = [
                'title' => $this->title,
                'description' => $this->message,
            ];
        }

        if ($this->url && ! $this->message) {
            $embeds = [
                'title' => $this->title,
            ];
        }

        if ($this->url) {
            $embeds['url'] = $this->url;
        }

        if ($this->image) {
            $embeds['image'] = [
                'url' => $this->image,
            ];
        }

        if (count($embeds) > 0) {
            $params = [
                'content' => '@everyone',
                'embeds' => [
                    $embeds,
                ],
            ];
        } else {
            $params = [
                'content' => $this->title,
            ];
        }

        dispatch(new NotifyDiscordJob($params));

        Discord::send($params);
    }

    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public function handler(Exceptions $exceptions): void
    {
        $exceptions->reportable(function (Throwable $e) {
            if (config('laravel-discord-error-tracker.error-webhook-active')) {
                try {
                    dispatch(new NotifyDiscordJob([
                        'webhook' => config('filament-discord-driver.error-webhook'),
                        'title' => $e->getMessage(),
                        'message' => collect([
                            'File: ' . $e->getFile(),
                            'Line: ' . $e->getLine(),
                            'Time: ' . \Carbon\Carbon::now()->toDateTimeString(),
                            'Trace: ```' . str($e->getTraceAsString())->limit(2500) . '```',
                        ])->implode("\n"),
                        'url' => url()->current(),
                    ]));
                } catch (\Exception $exception) {
                    // do nothing
                }
            }
        });
    }
}
