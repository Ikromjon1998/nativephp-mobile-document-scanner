# Epic 1: Gallery Import

**Priority:** High
**Status:** Not Started

## Goal

Allow users to import existing photos from the device gallery as scanned documents, instead of requiring a live camera scan.

## Background

Android ML Kit's `GmsDocumentScannerOptions` supports `setGalleryImportAllowed(true)`, which adds a gallery button to the scanner UI. This is currently hardcoded to `false`. iOS VisionKit does not natively support gallery import — this epic is Android-only.

## Acceptance Criteria

- [ ] New `galleryImport` parameter in `ScanOptions` DTO (default: `false`)
- [ ] PHP validation allows boolean `galleryImport` option
- [ ] Android passes `setGalleryImportAllowed()` based on parameter
- [ ] iOS ignores the parameter gracefully (no error)
- [ ] Config option `default_gallery_import` added to publishable config
- [ ] JS `scan()` accepts `galleryImport` option
- [ ] Tests cover the new parameter
- [ ] Documentation updated

## Implementation Steps

### Step 1: PHP Layer

1. Add `galleryImport` property to `ScanOptions` DTO:
   ```php
   public bool $galleryImport = false,
   ```

2. Add to `ScanOptions::toArray()` output

3. Add validation in `ScanValidator` (must be boolean)

4. Add `default_gallery_import` to `config/document-scanner.php`

5. Add to `nativeConfig()` in `DocumentScanner.php`

### Step 2: Android Native

1. In `DocumentScannerFunctions.kt`, read `galleryImport` parameter:
   ```kotlin
   val galleryImport = parameters["galleryImport"] as? Boolean ?: defaultGalleryImport
   ```

2. Pass to scanner options:
   ```kotlin
   optionsBuilder.setGalleryImportAllowed(galleryImport)
   ```

3. Add `defaultGalleryImport` to `applyConfig()`

### Step 3: iOS Native

1. No changes needed — VisionKit doesn't support gallery import
2. Parameter is simply ignored on iOS

### Step 4: JavaScript

1. Add `galleryImport` to `scan()` JSDoc parameters

### Step 5: Tests & Docs

1. Add tests for `galleryImport` in `DocumentScannerTest.php` and `DataTest.php`
2. Update `docs/api-reference.md` and `docs/configuration.md`
3. Note in README that gallery import is Android-only
