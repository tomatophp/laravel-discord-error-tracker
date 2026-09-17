<?php

use Illuminate\Support\Facades\Http;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

use function PHPUnit\Framework\assertTrue;

it('can send discord error', function () {
    Http::fake();

    $exception = new Exception('Test Exception');
    $discordServices = new DiscordServices;
    $response = $discordServices->handler($exception);

    assertTrue($response);
    Http::assertSentCount(1);
});
