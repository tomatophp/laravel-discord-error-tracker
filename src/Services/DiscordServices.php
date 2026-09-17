<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Carbon;
use Throwable;
use TomatoPHP\LaravelDiscordErrorTracker\Exceptions\DiscordWebhookException;
use TomatoPHP\LaravelDiscordErrorTracker\Jobs\NotifyDiscordJob;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbedFooter;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbeds;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordField;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordLimits;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordMessage;
use WeakMap;

class DiscordServices
{
    /**
     * Guards against reporting an exception thrown while reporting.
     */
    private static bool $reporting = false;

    /**
     * Exception handlers that already have the Discord reportable callback.
     *
     * @var WeakMap<object, bool>|null
     */
    private static ?WeakMap $registeredHandlers = null;

    /**
     * Report an exception now, or register the reportable callback on a Laravel 11+ `Exceptions` configurator.
     */
    public static function handler(Exceptions | Throwable $exceptions): bool
    {
        if ($exceptions instanceof Exceptions) {
            return static::registerReportable($exceptions->handler);
        }

        return static::report($exceptions);
    }

    /**
     * Register the Discord reportable callback on an exception handler (once per handler).
     */
    public static function registerReportable(object $handler): bool
    {
        if (! method_exists($handler, 'reportable')) {
            return false;
        }

        self::$registeredHandlers ??= new WeakMap;

        if (isset(self::$registeredHandlers[$handler])) {
            return true;
        }

        self::$registeredHandlers[$handler] = true;

        // The callback returns nothing, so the application's own logging keeps running.
        $handler->reportable(function (Throwable $exception): void {
            static::report($exception);
        });

        return true;
    }

    public static function isEnabled(): bool
    {
        return (bool) config('laravel-discord-error-tracker.error-webhook-active')
            && filled(config('laravel-discord-error-tracker.error-webhook'));
    }

    /**
     * Queue a Discord notification for the exception. Never throws.
     */
    public static function report(Throwable $exception): bool
    {
        if (self::$reporting || ! static::isEnabled() || static::shouldIgnore($exception)) {
            return false;
        }

        self::$reporting = true;

        try {
            NotifyDiscordJob::dispatch(static::payload($exception));

            return true;
        } catch (Throwable) {
            return false;
        } finally {
            self::$reporting = false;
        }
    }

    /**
     * Failures of the Discord webhook itself are never sent to Discord.
     */
    public static function shouldIgnore(Throwable $exception): bool
    {
        do {
            if ($exception instanceof DiscordWebhookException) {
                return true;
            }
        } while ($exception = $exception->getPrevious());

        return false;
    }

    /**
     * Build the Discord webhook payload for an exception.
     *
     * @return array{content: string|null, embeds: array<int, array<string, mixed>>}
     */
    public static function payload(Throwable $exception): array
    {
        $traceLimit = min((int) config('laravel-discord-error-tracker.trace-limit', 4000), DiscordLimits::DESCRIPTION - 6);
        $url = static::currentUrl();

        $embed = DiscordEmbeds::make(filled($exception->getMessage()) ? $exception->getMessage() : $exception::class)
            ->color('#ED4245')
            ->message('```' . DiscordLimits::truncate($exception->getTraceAsString(), $traceLimit) . '```')
            ->fields([
                DiscordField::make('Exception', $exception::class),
                DiscordField::make('File', $exception->getFile()),
                DiscordField::make('Line', $exception->getLine()),
                DiscordField::make('URL', $url),
            ])
            ->footer(
                DiscordEmbedFooter::make('Laravel Discord Error Tracker')
                    ->icon_url('https://tomatophp.com/tomato.png')
                    ->timestamp(Carbon::now()->toDateTimeString())
            );

        if ($url) {
            $embed->url($url);
        }

        return DiscordMessage::make()->embeds([$embed])->toArray();
    }

    private static function currentUrl(): ?string
    {
        try {
            return url()->current();
        } catch (Throwable) {
            return null;
        }
    }
}
