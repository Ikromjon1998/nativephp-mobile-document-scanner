# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.5.0-beta.1] - 2026-10-03

> **Pre-release.** The native fixes below compile in a NativePHP Mobile v4 app on iOS and
> Android, and the PDF functions run on the iOS simulator, but scanning itself has not yet been
> run on a physical device — the scanner camera is not available in simulators. Install with
> `composer require ikromjon/nativephp-mobile-document-scanner:^1.5@beta`. 1.5.0 follows once a
> device scan confirms them.

### Added

- NativePHP for Mobile v4 support — widened the `nativephp/mobile` constraint to `^3.0|^4.0`
  so the package installs on apps running v4. The bridge, event and manifest contracts the
  plugin uses are unchanged in v4, so no PHP or native code changed for this

### Fixed

- `jpegQuality` is now honoured on Android — scanned pages are re-encoded at the requested
  quality instead of being copied verbatim from ML Kit (the option was previously a no-op)
- `maxPages` is now enforced on iOS — VisionKit has no page-limit API, so the plugin truncates
  to the first `maxPages` pages after scanning (the option was previously a no-op)
- iOS PDF output no longer stretches every page to US Letter (612x792); each page is now sized
  to its source image, matching Android
- `pageCount` in `DocumentScanned` now reports the number of scanned pages on both platforms
  for PDF output (iOS previously reported `1`, the number of files)

### Changed

- **Breaking:** raised platform baselines to the NativePHP Mobile v3 minimums —
  Android `min_version` 21 → 29, iOS `min_version` 13.0 → 18.0
- Declared `illuminate/support` (`^11.0|^12.0|^13.0`) as an explicit dependency instead of
  relying on transitive resolution
- Removed the `ocr` keyword from `composer.json` — the plugin does not perform OCR
- Bumped `orchestra/testbench` to `^10.0|^11.0` (Laravel 12/13). The previous `^9.0`
  constraint resolved only to Laravel 11, which Composer now refuses to install because
  every 11.x release carries an unresolved security advisory
- CI now tests `nativephp/mobile` `^3.0` and `^4.0` explicitly on PHP 8.3 and 8.4, and reports
  one aggregate `tests` check. Without the explicit axis every job resolves v4, because
  `nativephp/mobile` 4.5.0 restored PHP 8.3 support

## [1.4.0] - 2026-04-10

### Added

- `imagesToPdf(array $paths, ?string $outputPath = null)` — combine JPEG images into a single PDF using native platform APIs (Android `PdfDocument`, iOS `UIGraphicsPDFRenderer`)
- `pdfToImages(string $pdfPath, ?int $quality = 80)` — extract page thumbnails from a PDF as JPEG images (Android `PdfRenderer`, iOS `PDFDocument`)
- `PdfCreated` event dispatched when `imagesToPdf()` completes
- `imagesToPdf()` and `pdfToImages()` added to `DocumentScannerInterface` contract
- JS wrappers: `imagesToPdf(paths, outputPath?)` and `pdfToImages(pdfPath, quality?)`
- Bridge functions: `DocumentScanner.ImagesToPdf` and `DocumentScanner.PdfToImages`
- Input validation for both methods with `InvalidArgumentException` on failure

## [1.3.0] - 2026-04-09

### Added

- Warning log when `scan()` is called outside a NativePHP native build
- Graceful handling of `json_encode` failure in bridge calls
- Troubleshooting section in installation docs
- Quick-start example at the top of README for faster onboarding
- Full Livewire component example in README
- Scanned files and required permissions sections in README
- Platform column in scan parameters table to clarify Android-only options

### Changed

- Replaced fake native code lint with real ktlint (Kotlin) and SwiftLint (Swift)
- Bumped `actions/checkout` to v5 in CI workflow
- Raised code coverage threshold from 90% to 95%
- Fixed Android dependencies format in `nativephp.json`

## [1.2.0] - 2026-04-05

### Added

- Scanner mode selection (`scannerMode`) — choose between `base`, `filter`, or `full` ML Kit processing (Android only)
- `ScannerMode` enum with `Base`, `Filter`, `Full` cases
- `default_scanner_mode` config option in publishable config
- `scannerMode` property in `ScanOptions` DTO
- Tests for scanner mode across DTO, bridge, config, and validation layers

## [1.1.0] - 2026-04-05

### Added

- Gallery import option (`galleryImport`) — allow users to import photos from device gallery (Android only)
- `default_gallery_import` config option in publishable config
- `galleryImport` property in `ScanOptions` DTO
- Tests for gallery import across DTO, bridge, and config layers

## [1.0.0] - 2026-04-05

### Added

- Document scanning with native platform APIs (VisionKit on iOS, ML Kit on Android)
- Automatic edge detection, perspective correction, and cropping
- Multi-page scanning support
- Output as JPEG images or PDF
- Configurable JPEG quality (1-100)
- Configurable page limits with safety cap
- `ScanOptions` DTO for type-safe scan configuration
- `OutputFormat` enum (`Jpeg`, `Pdf`)
- `DocumentScanned`, `ScanCancelled`, `ScanFailed` events
- Publishable config file with all defaults
- JavaScript client library with event constants
- Boost AI guidelines
- Pest test suite with full coverage
- `declare(strict_types=1)` in all PHP files

[1.5.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.5.0
[1.4.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.4.0
[1.3.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.3.0
[1.2.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.2.0
[1.1.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.1.0
[1.0.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.0.0
