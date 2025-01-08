<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordMessage
{
    public ?string $content = null;

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

    public function embeds(array $embeds): self
    {
        $getEmbeds = [];
        foreach ($embeds as $embed) {
            if ($embed instanceof DiscordEmbeds) {
                $getEmbeds[] = $embed->toArray();
            }
        }
        $this->embeds = $getEmbeds;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'embeds' => array_values($this->embeds),
        ];
    }
}
