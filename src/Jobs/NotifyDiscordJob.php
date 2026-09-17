<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use TomatoPHP\LaravelDiscordErrorTracker\Clients\Discord;

class NotifyDiscordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * @param  array<string, mixed>  $params
     */
    public function __construct(
        public array $params,
        public ?string $webhook = null,
    ) {}

    public function handle(): void
    {
        Discord::send($this->params, $this->webhook);
    }
}
