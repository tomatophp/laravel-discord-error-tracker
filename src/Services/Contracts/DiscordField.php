<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordField
{
    public string $name = '';

    public string $value = '';

    public bool $inline = false;

    public static function make(?string $name = null, string | int | null $value = null): self
    {
        return (new self)->name((string) $name)->value((string) $value);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function inline(bool $inline = true): self
    {
        $this->inline = $inline;

        return $this;
    }

    /**
     * Discord rejects empty field names and values, so they fall back to "-".
     *
     * @return array{name: string, value: string, inline: bool}
     */
    public function toArray(): array
    {
        return [
            'name' => DiscordLimits::truncate(filled($this->name) ? $this->name : '-', DiscordLimits::FIELD_NAME),
            'value' => DiscordLimits::truncate(filled($this->value) ? $this->value : '-', DiscordLimits::FIELD_VALUE),
            'inline' => $this->inline,
        ];
    }
}
