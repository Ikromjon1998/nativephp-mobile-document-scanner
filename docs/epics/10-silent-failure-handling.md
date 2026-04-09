# Epic 10: Silent Failure Handling

**Priority:** High
**Status:** Done

## Goal

Provide clear feedback when the scanner is called outside a native context (e.g., during local development with `php artisan serve` or in tests without mocking).

## Background

Currently, `DocumentScanner::scan()` calls `nativephp_call()` which returns `[]` silently when the NativePHP runtime is not available. A developer testing locally gets no error, no warning, and no indication of what went wrong. This leads to confusion — "I called scan but nothing happened."

The `nativephp_call()` function is stubbed as a no-op in the test helper, but a developer building their app locally has no such stub and gets zero feedback.

## Acceptance Criteria

- [x] `DocumentScanner::scan()` logs a warning when `nativephp_call()` is unavailable
- [x] Warning message clearly states: "Document scanner requires a NativePHP native build. Run: php artisan native:run android"
- [x] Warning uses Laravel's `logger()->warning()` (not an exception — don't break the app)
- [x] ~~Optional: `DocumentScanner::isAvailable(): bool` method~~ — Decided against exposing publicly; the bridge check is internal to `call()`. No real-world use case for calling it from app code since the app always runs in a native build in production.
- [x] Tests verify `scan()` returns `[]` when bridge is unavailable
- [x] Documentation updated with troubleshooting section

## Implementation Steps

### Step 1: Add Runtime Check with Warning Log

In `DocumentScanner::call()`, the existing `function_exists` guard now logs a warning via `logger()->warning()` before returning `[]`.

### Step 2: Update Docs

Added "Troubleshooting" section to `docs/installation.md`:
- "scan() does nothing" → need native build, check Laravel log for warning
- "No events received" → check `#[OnNative]` attribute and event imports
