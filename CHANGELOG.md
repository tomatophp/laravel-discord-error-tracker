# 2.0.0

- Support Laravel 12 and 13 on PHP 8.2+ (dropped Laravel 10/11; the 1.x line lives on the `v1` branch).
- Exceptions are reported automatically: the service provider registers a `reportable` callback on the application's exception handler, so no change to `bootstrap/app.php` is needed (`DISCORD_ERROR_AUTO_REPORT=false` to opt out). The old `DiscordServices::handler($exceptions)` call still works and never registers twice.
- The reportable callback no longer returns `false`, which used to stop Laravel's own exception logging.
- A failing or unreachable Discord webhook never crashes the app and is never reported back to Discord (no report loop). Failures raise `DiscordWebhookException` inside the queued job, which retries 3 times.
- Nothing is sent when the tracker is disabled or no webhook URL is set.
- Payloads respect Discord limits: title 256, description 4096 (trace code block stays closed), 25 fields, field value 1024, content 2000, 10 embeds, 6000 characters per embed.
- Fixed a `TypeError` on empty field values, a PHP deprecation on the `#` color prefix, an empty title for exceptions without a message, and invalid embed URLs.
- New config keys: `auto-report`, `trace-limit`, `timeout`.
- New Pest test suite on Orchestra Testbench; the webhook is always faked in tests.

# V1.0.0

First release of the package
