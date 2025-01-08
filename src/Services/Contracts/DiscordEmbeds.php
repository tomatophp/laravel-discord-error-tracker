<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordEmbeds
{
    public string $title;

    public ?array $fields = [];

    public ?string $message = null;

    public ?string $url = null;

    public ?string $image = null;

    public ?string $color = null;

    public ?DiscordEmbedFooter $footer = null;

    public static function make(string $title): self
    {
        return (new self)->title($title);
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function footer(DiscordEmbedFooter $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return $this
     */
    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return $this
     */
    public function image(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function fields(array $fields): self
    {
        $getFields = [];
        foreach ($fields as $field) {
            if ($field instanceof DiscordField) {
                $getFields[] = $field->toArray();
            }
        }
        $this->fields = $getFields;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
        ];

        if ($this->message) {
            $data['description'] = $this->message;
        }

        if ($this->url) {
            $data['url'] = $this->url;
        }

        if ($this->image) {
            $data['image'] = [
                'url' => $this->image,
            ];
        }

        if (count($this->fields)) {
            $data['fields'] = $this->fields;
        }

        if ($this->color) {
            $data['color'] = hexdec($this->color);
        }

        if ($this->footer) {
            $data['footer'] = $this->footer->toArray();
        }

        return $data;
    }
}
