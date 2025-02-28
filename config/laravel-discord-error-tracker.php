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
     * Allow Discord Errors Logger Webhook
     * ---------------------------------------
     */
    'everyone' => env('DISCORD_ERROR_EVERYONE', true),
];
