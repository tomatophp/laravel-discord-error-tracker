<?php

it('can send discord error', function () {
    $exception = new \Exception('Test Exception');
    $discordServices = new \TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;
    $response = $discordServices->handler($exception);

    $this->assertTrue($response);
});
