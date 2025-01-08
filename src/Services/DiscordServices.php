<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services;

use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;
use TomatoPHP\LaravelDiscordErrorTracker\Jobs\NotifyDiscordJob;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbedFooter;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbeds;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordField;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordMessage;

class DiscordServices
{
    private ?string $title;

    private ?string $file;

    private ?string $line;

    private ?string $trace;

    private ?string $time;

    private ?string $url;

    private ?int $traceLimit = 4000;

    private function send(): void
    {
        $params = DiscordMessage::make()
            ->embeds([
                DiscordEmbeds::make($this->title)
                    ->color('#ED4245')
                    ->message($this->trace)
                    ->fields([
                        DiscordField::make('File', $this->file),
                        DiscordField::make('Line', $this->line),
                        DiscordField::make('URL', $this->url),
                    ])
                    ->footer(
                        DiscordEmbedFooter::make('Laravel Discord Error Tracker')
                            ->icon_url('https://tomatophp.com/tomato.png')
                            ->timestamp($this->time)
                    )
                    ->url($this->url),
            ])
            ->toArray();

        dispatch(new NotifyDiscordJob($params));
    }

    private function exception(Throwable $e): void
    {
        $this->title = $e->getMessage();
        $this->file = $e->getFile();
        $this->line = $e->getLine();
        $this->time = \Carbon\Carbon::now()->toDateTimeString();
        $this->trace = '```' . str($e->getTraceAsString())->limit($this->traceLimit) . '```';
        $this->url = url()->current();

        $this->send();
    }

    public function handler(Exceptions | Throwable $exceptions): bool
    {
        if (config('laravel-discord-error-tracker.error-webhook-active')) {
            if ($exceptions instanceof Exceptions) {
                $exceptions->reportable(function (Throwable $e) {
                    try {
                        $this->exception($e);

                        return true;
                    } catch (\Exception $exception) {
                        return false;
                    }
                });
            } else {
                try {
                    $this->exception($exceptions);

                    return true;
                } catch (\Exception $exception) {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}
