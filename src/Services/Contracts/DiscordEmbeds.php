<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

class DiscordEmbeds
{
    public string $title = '';

    /**
     * @var array<int, array{name: string, value: string, inline: bool}>
     */
    public array $fields = [];

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

    /**
     * Discord accepts at most 25 fields per embed; extra fields are dropped.
     *
     * @param  array<int, mixed>  $fields
     */
    public function fields(array $fields): self
    {
        $getFields = [];
        foreach ($fields as $field) {
            if ($field instanceof DiscordField) {
                $getFields[] = $field->toArray();
            }
        }
        $this->fields = array_slice($getFields, 0, DiscordLimits::FIELDS);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'title' => DiscordLimits::truncate(filled($this->title) ? $this->title : 'Error', DiscordLimits::TITLE),
        ];

        if ($this->url && filter_var($this->url, FILTER_VALIDATE_URL)) {
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
            $data['color'] = (int) hexdec(ltrim($this->color, '#'));
        }

        if ($this->footer) {
            $data['footer'] = $this->footer->toArray();
        }

        if ($this->message) {
            $data['description'] = DiscordLimits::truncateKeepingCodeBlock(
                $this->message,
                max(0, min(DiscordLimits::DESCRIPTION, DiscordLimits::EMBED_TOTAL - $this->countedLength($data)))
            );
        }

        return $data;
    }

    /**
     * Characters Discord counts toward the 6000 characters embed total.
     *
     * @param  array<string, mixed>  $data
     */
    private function countedLength(array $data): int
    {
        $length = mb_strlen($data['title']) + mb_strlen($data['footer']['text'] ?? '');

        foreach ($data['fields'] ?? [] as $field) {
            $length += mb_strlen($field['name']) + mb_strlen($field['value']);
        }

        return $length;
    }
}
