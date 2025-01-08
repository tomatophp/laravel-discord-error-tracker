<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordEmbedFooter
{
    public string $text;

    public ?string $timestamp = null;

    public ?string $icon_url = null;

    public static function make(string $text): self
    {
        return (new self)->text($text);
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function timestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function icon_url(string $icon_url): self
    {
        $this->icon_url = $icon_url;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text . ($this->timestamp ? (' - ' . $this->timestamp) : null),
            'icon_url' => $this->icon_url,
        ];
    }
}
