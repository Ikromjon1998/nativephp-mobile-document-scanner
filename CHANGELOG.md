# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Warning log when `scan()` is called outside a NativePHP native build
- Troubleshooting section in installation docs
- Quick-start example at the top of README for faster onboarding
- Full Livewire component example in README
- Scanned files and required permissions sections in README
- Platform column in scan parameters table to clarify Android-only options

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

[1.2.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.2.0
[1.1.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.1.0
[1.0.0]: https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/releases/tag/v1.0.0
