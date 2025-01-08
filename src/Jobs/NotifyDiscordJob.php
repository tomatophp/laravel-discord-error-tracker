<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TomatoPHP\LaravelDiscordErrorTracker\Clients\Discord;

class NotifyDiscordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public array $params
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Discord::send($this->params);
    }
}
