<?php

return [
    /**
     * ---------------------------------------
     * Allow Discord Errors Logger Webhook
     * ---------------------------------------
     */
    'error-webhook-active' => env('DISCORD_ERROR_WEBHOOK_ACTIVE', false),

    /**
     * ---------------------------------------
     * Main Error Logger Discord Channel Webhook
     * ---------------------------------------
     */
    'error-webhook' => env('DISCORD_ERROR_WEBHOOK'),

    /**
     * ---------------------------------------
     * Mention @everyone on every error message
     * ---------------------------------------
     */
    'everyone' => env('DISCORD_ERROR_EVERYONE', true),

    /**
     * ---------------------------------------
     * Report every exception automatically
     * ---------------------------------------
     * Registers a reportable callback on the application's exception handler,
     * so no change to bootstrap/app.php is needed.
     */
    'auto-report' => env('DISCORD_ERROR_AUTO_REPORT', true),

    /**
     * ---------------------------------------
     * Max trace characters in the message (Discord limit is 4096)
     * ---------------------------------------
     */
    'trace-limit' => (int) env('DISCORD_ERROR_TRACE_LIMIT', 4000),

    /**
     * ---------------------------------------
     * Webhook request timeout in seconds
     * ---------------------------------------
     */
    'timeout' => (int) env('DISCORD_ERROR_TIMEOUT', 10),
];
