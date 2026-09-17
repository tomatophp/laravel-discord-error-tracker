<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use TomatoPHP\LaravelDiscordErrorTracker\Exceptions\DiscordWebhookException;
use TomatoPHP\LaravelDiscordErrorTracker\Jobs\NotifyDiscordJob;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;
use TomatoPHP\LaravelDiscordErrorTracker\Tests\TestCase;

it('sends the expected webhook payload when the app reports an exception', function () {
    Http::fake();

    report(new RuntimeException('Boom'));

    Http::assertSentCount(1);
    Http::assertSent(function (Request $request) {
        $embed = $request['embeds'][0];

        return $request->url() === TestCase::WEBHOOK
            && $request->method() === 'POST'
            && $request['content'] === '@everyone'
            && $embed['title'] === 'Boom'
            && $embed['color'] === 0xED4245
            && str_starts_with($embed['description'], '```')
            && $embed['fields'][0] === ['name' => 'Exception', 'value' => RuntimeException::class, 'inline' => false]
            && $embed['fields'][1]['name'] === 'File'
            && $embed['fields'][2]['name'] === 'Line'
            && $embed['fields'][3]['name'] === 'URL'
            && str_starts_with($embed['footer']['text'], 'Laravel Discord Error Tracker');
    });
});

it('queues the notify job when the app reports an exception', function () {
    Queue::fake();

    report(new RuntimeException('Queued boom'));

    Queue::assertPushed(NotifyDiscordJob::class, fn (NotifyDiscordJob $job) => $job->params['embeds'][0]['title'] === 'Queued boom');
});

it('keeps the default exception logging running', function () {
    Http::fake();
    Log::spy();

    report(new RuntimeException('Still logged'));

    Log::shouldHaveReceived('error')->withArgs(fn (string $message) => $message === 'Still logged')->once();
    Http::assertSentCount(1);
});

it('sends nothing when the tracker is disabled', function () {
    Http::fake();
    config()->set('laravel-discord-error-tracker.error-webhook-active', false);

    report(new RuntimeException('Boom'));

    Http::assertNothingSent();
});

it('sends nothing when no webhook is configured', function () {
    Http::fake();
    Queue::fake();
    config()->set('laravel-discord-error-tracker.error-webhook', null);

    report(new RuntimeException('Boom'));

    Queue::assertNothingPushed();
    Http::assertNothingSent();
});

it('does not register the reportable callback when auto-report is off', function () {
    Http::fake();
    config()->set('laravel-discord-error-tracker.auto-report', false);
    app()->forgetInstance(ExceptionHandler::class);

    report(new RuntimeException('Boom'));

    Http::assertNothingSent();
});

it('never crashes the app when discord responds with an error', function () {
    Http::fake(['*' => Http::response(['message' => 'Unknown Webhook'], 404)]);

    report(new RuntimeException('Boom'));

    Http::assertSentCount(1);
});

it('never crashes the app when discord is unreachable', function () {
    Http::fake(fn () => throw new ConnectionException('Could not resolve host'));

    expect(DiscordServices::report(new RuntimeException('Boom')))->toBeFalse();
});

it('does not report webhook failures back to discord', function () {
    Queue::fake();

    report(new DiscordWebhookException('Webhook down'));
    report(new RuntimeException('Wrapped', 0, new DiscordWebhookException('Webhook down')));

    Queue::assertNothingPushed();
});

it('registers the callback only once when also wired in bootstrap/app.php', function () {
    Http::fake();

    expect(DiscordServices::handler(new Exceptions(app(ExceptionHandler::class))))->toBeTrue();

    report(new RuntimeException('Boom'));

    Http::assertSentCount(1);
});

it('still reports a single exception directly', function () {
    Http::fake();

    expect(DiscordServices::handler(new RuntimeException('Direct')))->toBeTrue();

    Http::assertSent(fn (Request $request) => $request['embeds'][0]['title'] === 'Direct');
});
