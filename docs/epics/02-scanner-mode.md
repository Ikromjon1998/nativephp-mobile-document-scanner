# Epic 2: Scanner Mode Selection

**Priority:** Medium
**Status:** Done

## Goal

Allow users to choose the ML Kit scanner processing mode: `base` (fast, no filters), `filter` (adds color filters), or `full` (adds ML-enhanced cleaning). Currently hardcoded to `SCANNER_MODE_FULL`.

## Background

Google ML Kit Document Scanner offers three modes:
- `SCANNER_MODE_BASE` — Fastest, minimal processing
- `SCANNER_MODE_BASE_WITH_FILTER` — Adds grayscale/color filter options
- `SCANNER_MODE_FULL` — Full ML enhancement (current default)

iOS VisionKit doesn't have equivalent modes — it always applies full processing.

## Acceptance Criteria

- [x] New `ScannerMode` enum with `Base`, `Filter`, `Full` cases
- [x] New `scannerMode` parameter in `ScanOptions` DTO (default: `Full`)
- [x] PHP validation accepts valid mode strings
- [x] Android maps mode to `GmsDocumentScannerOptions` constant
- [x] iOS ignores the parameter gracefully
- [x] Config option `default_scanner_mode` added
- [x] JS `scan()` accepts `scannerMode` option
- [x] Tests cover all three modes
- [x] Documentation updated

## Implementation Steps

### Step 1: PHP Layer

1. Create `src/Enums/ScannerMode.php`:
   ```php
   enum ScannerMode: string
   {
       case Base = 'base';
       case Filter = 'filter';
       case Full = 'full';
   }
   ```

2. Add `scannerMode` to `ScanOptions` DTO:
   ```php
   public ScannerMode|string $scannerMode = ScannerMode::Full,
   ```

3. Add enum-to-string conversion in `toArray()`

4. Add validation in `ScanValidator`:
   ```php
   if (isset($options['scannerMode']) && !in_array($options['scannerMode'], ['base', 'filter', 'full'])) {
       throw new \InvalidArgumentException('scannerMode must be base, filter, or full');
   }
   ```

5. Add `default_scanner_mode` to config and `nativeConfig()`

### Step 2: Android Native

1. Read `scannerMode` parameter in `Scan.execute()`:
   ```kotlin
   val scannerMode = parameters["scannerMode"] as? String ?: defaultScannerMode
   ```

2. Map to ML Kit constant:
   ```kotlin
   val mode = when (scannerMode) {
       "base" -> GmsDocumentScannerOptions.SCANNER_MODE_BASE
       "filter" -> GmsDocumentScannerOptions.SCANNER_MODE_BASE_WITH_FILTER
       else -> GmsDocumentScannerOptions.SCANNER_MODE_FULL
   }
   optionsBuilder.setScannerMode(mode)
   ```

3. Add `defaultScannerMode` to `applyConfig()`

### Step 3: iOS Native

1. No changes — parameter is ignored on iOS

### Step 4: JavaScript & Docs

1. Add `scannerMode` to `scan()` JSDoc
2. Update API reference, configuration docs, and README
3. Note that scanner mode is Android-only
