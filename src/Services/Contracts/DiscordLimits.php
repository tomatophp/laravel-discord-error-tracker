<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Services\Contracts;

/**
 * Discord webhook payload limits.
 *
 * @see https://discord.com/developers/docs/resources/message#embed-object-embed-limits
 */
class DiscordLimits
{
    public const CONTENT = 2000;

    public const EMBEDS = 10;

    public const EMBED_TOTAL = 6000;

    public const TITLE = 256;

    public const DESCRIPTION = 4096;

    public const FIELDS = 25;

    public const FIELD_NAME = 256;

    public const FIELD_VALUE = 1024;

    public const FOOTER_TEXT = 2048;

    /**
     * Cut a string to a maximum number of characters, ending with "...".
     */
    public static function truncate(string $value, int $limit): string
    {
        if ($limit <= 0) {
            return '';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        if ($limit <= 3) {
            return mb_substr($value, 0, $limit);
        }

        return mb_substr($value, 0, $limit - 3) . '...';
    }

    /**
     * Truncate a string, keeping a surrounding ``` code block closed.
     */
    public static function truncateKeepingCodeBlock(string $value, int $limit): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        if ($limit > 6 && mb_strlen($value) > 6 && str_starts_with($value, '```') && str_ends_with($value, '```')) {
            return '```' . static::truncate(mb_substr($value, 3, -3), $limit - 6) . '```';
        }

        return static::truncate($value, $limit);
    }
}
