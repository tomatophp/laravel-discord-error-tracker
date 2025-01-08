<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordField
{
    public string $name;

    public string $value;

    public ?bool $inline = false;

    public static function make(?string $name = null, ?string $value = null): self
    {
        return (new self)->name($name)->value($value);
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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ];
    }
}
