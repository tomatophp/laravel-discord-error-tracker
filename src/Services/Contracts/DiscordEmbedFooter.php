<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordEmbedFooter
{
    public string $text = '';

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

    /**
     * @return array{text: string, icon_url?: string}
     */
    public function toArray(): array
    {
        $data = [
            'text' => DiscordLimits::truncate($this->text . ($this->timestamp ? (' - ' . $this->timestamp) : ''), DiscordLimits::FOOTER_TEXT),
        ];

        if ($this->icon_url) {
            $data['icon_url'] = $this->icon_url;
        }

        return $data;
    }
}
