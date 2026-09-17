<?php

namespace TomatoPHP\LaravelDiscordErrorTracker\Exceptions;

use RuntimeException;

/**
 * Thrown when the Discord webhook call fails.
 *
 * The package never reports this exception back to Discord, so a broken
 * webhook can not trigger an endless report / notify loop.
 */
class DiscordWebhookException extends RuntimeException {}
