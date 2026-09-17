<?php

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\LaravelDiscordErrorTracker\Clients\Discord;
use TomatoPHP\LaravelDiscordErrorTracker\Exceptions\DiscordWebhookException;
use TomatoPHP\LaravelDiscordErrorTracker\Jobs\NotifyDiscordJob;
use TomatoPHP\LaravelDiscordErrorTracker\Tests\TestCase;

it('posts the payload to the configured webhook', function () {
    Http::fake();

    Discord::send(['content' => 'Hello']);

    Http::assertSent(fn (Request $request) => $request->url() === TestCase::WEBHOOK && $request['content'] === 'Hello');
});

it('posts to an explicit webhook', function () {
    Http::fake();

    Discord::send(['content' => 'Hello'], 'https://discord.test/api/webhooks/other');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://discord.test/api/webhooks/other');
});

it('throws a webhook exception when discord fails', function () {
    Http::fake(['*' => Http::response('Too Many Requests', 429)]);

    Discord::send(['content' => 'Hello']);
})->throws(DiscordWebhookException::class, 'HTTP 429');

it('throws a webhook exception when the connection fails', function () {
    Http::fake(fn () => throw new ConnectionException('timeout'));

    Discord::send(['content' => 'Hello']);
})->throws(DiscordWebhookException::class, 'timeout');

it('throws a webhook exception without a webhook url', function () {
    Http::fake();
    config()->set('laravel-discord-error-tracker.error-webhook', null);

    Discord::send(['content' => 'Hello']);
})->throws(DiscordWebhookException::class);

it('sends the payload from the queued job', function () {
    Http::fake();

    (new NotifyDiscordJob(['content' => 'From job'], 'https://discord.test/api/webhooks/job'))->handle();

    Http::assertSent(fn (Request $request) => $request->url() === 'https://discord.test/api/webhooks/job' && $request['content'] === 'From job');
});

it('is a retryable queued job', function () {
    $job = new NotifyDiscordJob([]);

    expect($job)->toBeInstanceOf(ShouldQueue::class)
        ->and($job->tries)->toBe(3);
});
