# Epic 10: Silent Failure Handling

**Priority:** High
**Status:** Not Started

## Goal

Provide clear feedback when the scanner is called outside a native context (e.g., during local development with `php artisan serve` or in tests without mocking).

## Background

Currently, `DocumentScanner::scan()` calls `nativephp_call()` which returns `[]` silently when the NativePHP runtime is not available. A developer testing locally gets no error, no warning, and no indication of what went wrong. This leads to confusion — "I called scan but nothing happened."

The `nativephp_call()` function is stubbed as a no-op in the test helper, but a developer building their app locally has no such stub and gets zero feedback.

## Acceptance Criteria

- [ ] `DocumentScanner::scan()` logs a warning when `nativephp_call()` is unavailable
- [ ] Warning message clearly states: "Document scanner requires a NativePHP native build. Run: php artisan native:run android"
- [ ] Warning uses Laravel's `Log::warning()` (not an exception — don't break the app)
- [ ] Optional: `DocumentScanner::isAvailable(): bool` method for developers to check at runtime
- [ ] Tests verify warning is logged when bridge is unavailable
- [ ] Documentation updated with troubleshooting section

## Implementation Steps

### Step 1: Add Runtime Check

In `DocumentScanner::scan()`, before calling the bridge:

```php
if (! function_exists('nativephp_call')) {
    Log::warning('DocumentScanner: nativephp_call() is not available. The scanner requires a NativePHP native build. Run: php artisan native:run android|ios');
    return [];
}
```

### Step 2: Add `isAvailable()` Method

```php
public function isAvailable(): bool
{
    return function_exists('nativephp_call');
}
```

Add to `DocumentScannerInterface` and Facade docblock.

### Step 3: Update Docs

Add a "Troubleshooting" section to installation.md:
- "scan() does nothing" → need native build
- "No events received" → check event listener setup
