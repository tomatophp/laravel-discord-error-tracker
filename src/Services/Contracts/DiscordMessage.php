<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordMessage
{
    public ?string $content = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $embeds = [];

    public static function make(?string $content = null): self
    {
        return (new self)->content(config('laravel-discord-error-tracker.everyone') ? '@everyone' : $content);
    }

    public function content(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Discord accepts at most 10 embeds per message; extra embeds are dropped.
     *
     * @param  array<int, mixed>  $embeds
     */
    public function embeds(array $embeds): self
    {
        $getEmbeds = [];
        foreach ($embeds as $embed) {
            if ($embed instanceof DiscordEmbeds) {
                $getEmbeds[] = $embed->toArray();
            }
        }
        $this->embeds = array_slice($getEmbeds, 0, DiscordLimits::EMBEDS);

        return $this;
    }

    /**
     * @return array{content: string|null, embeds: array<int, array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'content' => filled($this->content) ? DiscordLimits::truncate($this->content, DiscordLimits::CONTENT) : null,
            'embeds' => array_values($this->embeds),
        ];
    }
}
