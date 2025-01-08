<?php

use function PHPUnit\Framework\assertTrue;

it('can send discord error', function () {
    $exception = new \Exception('Test Exception');
    $discordServices = new \TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;
    $response = $discordServices->handler($exception);

    assertTrue($response);
});
