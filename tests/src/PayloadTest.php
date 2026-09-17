<?php

use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbedFooter;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordEmbeds;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordField;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordLimits;
use TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts\DiscordMessage;
use TomatoPHP\LaravelDiscordErrorTracker\Services\DiscordServices;

function deepException(int $depth): Throwable
{
    return $depth === 0 ? new RuntimeException('Deep') : deepException($depth - 1);
}

it('converts the hex color without a deprecation', function () {
    expect(DiscordEmbeds::make('Title')->color('#ED4245')->toArray()['color'])->toBe(15548997);
});

it('accepts a null field value and an int line', function () {
    expect(DiscordField::make('Line', 42)->toArray()['value'])->toBe('42')
        ->and(DiscordField::make('URL', null)->toArray()['value'])->toBe('-');
});

it('truncates field names and values to the discord limits', function () {
    $field = DiscordField::make(str_repeat('n', 300), str_repeat('v', 2000))->toArray();

    expect(mb_strlen($field['name']))->toBe(256)
        ->and(mb_strlen($field['value']))->toBe(1024)
        ->and($field['value'])->toEndWith('...');
});

it('keeps at most 25 fields', function () {
    $fields = array_map(fn (int $i) => DiscordField::make("Field {$i}", 'value'), range(1, 30));

    expect(DiscordEmbeds::make('Title')->fields($fields)->toArray()['fields'])->toHaveCount(25);
});

it('truncates the title and falls back when empty', function () {
    expect(mb_strlen(DiscordEmbeds::make(str_repeat('t', 500))->toArray()['title']))->toBe(256)
        ->and(DiscordEmbeds::make('')->toArray()['title'])->toBe('Error');
});

it('keeps the description within 4096 characters and closes the code block', function () {
    $embed = DiscordEmbeds::make('Title')->message('```' . str_repeat('x', 10000) . '```')->toArray();

    expect(mb_strlen($embed['description']))->toBe(DiscordLimits::DESCRIPTION)
        ->and($embed['description'])->toStartWith('```')->toEndWith('...```');
});

it('keeps the whole embed within 6000 characters', function () {
    $embed = DiscordEmbeds::make(str_repeat('t', 300))
        ->message('```' . str_repeat('x', 10000) . '```')
        ->fields(array_map(fn () => DiscordField::make('Name', str_repeat('v', 1024)), range(1, 5)))
        ->footer(DiscordEmbedFooter::make('Footer'))
        ->toArray();

    $total = mb_strlen($embed['title']) + mb_strlen($embed['description']) + mb_strlen($embed['footer']['text'])
        + array_sum(array_map(fn (array $field) => mb_strlen($field['name']) + mb_strlen($field['value']), $embed['fields']));

    expect($total)->toBeLessThanOrEqual(DiscordLimits::EMBED_TOTAL);
});

it('drops an invalid embed url', function () {
    expect(DiscordEmbeds::make('Title')->url('not a url')->toArray())->not->toHaveKey('url');
});

it('truncates the message content and keeps at most 10 embeds', function () {
    config()->set('laravel-discord-error-tracker.everyone', false);

    $message = DiscordMessage::make(str_repeat('c', 3000))
        ->embeds(array_map(fn () => DiscordEmbeds::make('Title'), range(1, 12)))
        ->toArray();

    expect(mb_strlen($message['content']))->toBe(2000)
        ->and($message['embeds'])->toHaveCount(10);
});

it('mentions everyone only when enabled', function () {
    config()->set('laravel-discord-error-tracker.everyone', false);
    expect(DiscordMessage::make()->toArray()['content'])->toBeNull();

    config()->set('laravel-discord-error-tracker.everyone', true);
    expect(DiscordMessage::make()->toArray()['content'])->toBe('@everyone');
});

it('builds an exception payload that respects the discord limits', function () {
    $payload = DiscordServices::payload(new RuntimeException(str_repeat('m', 400), 0, deepException(200)));
    $embed = $payload['embeds'][0];

    expect($payload['embeds'])->toHaveCount(1)
        ->and(mb_strlen($embed['title']))->toBeLessThanOrEqual(256)
        ->and(mb_strlen($embed['description']))->toBeLessThanOrEqual(4096)
        ->and($embed['description'])->toEndWith('```')
        ->and($embed['fields'])->toHaveCount(4)
        ->and(json_encode($payload))->toBeString();
});

it('uses the exception class as title when the message is empty', function () {
    expect(DiscordServices::payload(new LogicException)['embeds'][0]['title'])->toBe(LogicException::class);
});

it('respects a smaller configured trace limit', function () {
    config()->set('laravel-discord-error-tracker.trace-limit', 50);

    $description = DiscordServices::payload(deepException(20))['embeds'][0]['description'];

    expect(mb_strlen($description))->toBeLessThanOrEqual(56);
});
