# Epic 11: Platform-Specific Option Hints

**Priority:** Medium
**Status:** Done

## Goal

Make it immediately obvious which scan options are platform-specific so developers don't set Android-only options on iOS and wonder why nothing changes.

## Background

`galleryImport` and `scannerMode` are Android-only options. The documentation mentions this, but:
- The `ScanOptions` DTO constructor shows no hint about platform specificity
- No runtime feedback is given when these options are set on iOS
- IDE autocompletion shows all options regardless of target platform

A developer building for iOS might set `scannerMode: 'base'` expecting faster scans, with no indication it's being ignored.

## Acceptance Criteria

- [x] PHPDoc on `galleryImport` and `scannerMode` properties includes `@note Android only`
- [x] `ScanOptions::toArray()` adds a `_platformNotes` key when Android-only options are set (for debug)
- [x] README and API reference clearly tag Android-only options with a badge or prefix
- [x] JS `scan()` JSDoc already notes Android-only (verified) — docs match

## Implementation Steps

### Step 1: Update ScanOptions PHPDoc

```php
/**
 * Allow importing images from the device gallery.
 * Android only — ignored on iOS.
 */
public bool $galleryImport = false,

/**
 * ML Kit scanner processing mode.
 * Android only — ignored on iOS.
 */
public ScannerMode|string $scannerMode = ScannerMode::Full,
```

### Step 2: Update Documentation Tables

In README.md and api-reference.md, prefix Android-only options:

| Parameter      | Type   | Platform     | Description               |
| -------------- | ------ | ------------ | ------------------------- |
| `galleryImport`| bool   | Android only | Allow gallery import      |
| `scannerMode`  | string | Android only | ML Kit processing mode    |

### Step 3: Consider Runtime Info Log (Optional)

Log an info-level message when Android-only options are set and the platform is iOS. This requires knowing the platform at runtime — may need NativePHP platform detection API.
