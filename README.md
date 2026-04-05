# NativePHP Mobile Document Scanner

[![Tests](https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/github/license/Ikromjon1998/nativephp-mobile-document-scanner)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-8.3%2B-8892BF)](composer.json)

Scan documents with automatic edge detection, perspective correction, and cropping in your NativePHP Mobile app — powered by native platform APIs.

## How it works

| Platform    | Native API                                   | Features                                                                |
| ----------- | -------------------------------------------- | ----------------------------------------------------------------------- |
| **iOS**     | VisionKit (`VNDocumentCameraViewController`) | Auto edge detection, perspective correction, shadow removal, multi-page |
| **Android** | Google ML Kit Document Scanner               | Auto edge detection, cropping, rotation, multi-page, gallery import     |

## Features

- Automatic edge detection and perspective correction
- Multi-page document scanning
- Output as JPEG images or PDF
- Configurable JPEG quality (1-100)
- Configurable page limits
- Events for scan completion, cancellation, and errors
- Works with Livewire, Inertia (Vue/React), and native UI
- No external API keys or internet required

## Installation

```bash
composer require ikromjon/nativephp-mobile-document-scanner

php artisan native:plugin:register ikromjon/nativephp-mobile-document-scanner
```

Build your app (plugin requires a native build):

```bash
php artisan native:run android
# or
php artisan native:run ios
```

## Configuration

Optionally publish the config file:

```bash
php artisan vendor:publish --tag=document-scanner-config
```

| Key                     | Default             | Description                                |
| ----------------------- | ------------------- | ------------------------------------------ |
| `default_max_pages`     | `0`                 | Default max pages per scan (0 = unlimited) |
| `max_pages_limit`       | `100`               | Absolute cap on pages per scan             |
| `default_output_format` | `jpeg`              | Default output format (`jpeg` or `pdf`)    |
| `default_jpeg_quality`  | `90`                | Default JPEG compression quality (1-100)   |
| `storage_directory`     | `scanned-documents` | Subdirectory for scanned files             |
| `default_gallery_import`| `false`             | Allow gallery import (Android only)        |
| `default_scanner_mode`  | `full`              | Scanner mode: `base`, `filter`, `full` (Android only) |

## Usage (PHP)

### Scan a Document

```php
use Ikromjon\DocumentScanner\Facades\DocumentScanner;

// Scan with defaults
DocumentScanner::scan();

// Scan with options
DocumentScanner::scan([
    'maxPages' => 3,
    'outputFormat' => 'jpeg',
    'jpegQuality' => 85,
]);

// Scan a single page (e.g. ID card)
DocumentScanner::scan(['maxPages' => 1]);

// Scan to PDF
DocumentScanner::scan(['outputFormat' => 'pdf']);
```

The `scan()` method opens the native scanner UI and returns immediately. Results are delivered asynchronously via events.

### Type-Safe DTO

```php
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;

DocumentScanner::scan(new ScanOptions(
    maxPages: 5,
    outputFormat: OutputFormat::Jpeg,
    jpegQuality: 90,
));
```

### Scan Parameters

| Parameter      | Type                 | Required | Description                               |
| -------------- | -------------------- | -------- | ----------------------------------------- |
| `maxPages`     | int                  | No       | Max pages to scan (0 = unlimited)         |
| `outputFormat` | OutputFormat\|string | No       | `jpeg` or `pdf`                           |
| `jpegQuality`  | int                  | No       | JPEG quality 1-100 (only for jpeg output) |
| `galleryImport`| bool                 | No       | Allow gallery import (Android only)       |
| `scannerMode`  | ScannerMode\|string  | No       | `base`, `filter`, or `full` (Android only)|

## Listening to Events (Livewire)

```php
use Native\Mobile\Attributes\OnNative;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;

#[OnNative(DocumentScanned::class)]
public function onScanned($data)
{
    // $data['paths'] — array of file paths
    // $data['pageCount'] — number of pages scanned
    // $data['outputFormat'] — 'jpeg' or 'pdf'
}

#[OnNative(ScanCancelled::class)]
public function onCancelled()
{
    // User cancelled the scanner
}

#[OnNative(ScanFailed::class)]
public function onFailed($data)
{
    // $data['error'] — error message
}
```

## Listening to Events (Laravel)

```php
use Ikromjon\DocumentScanner\Events\DocumentScanned;

class HandleDocumentScanned
{
    public function handle(DocumentScanned $event): void
    {
        // $event->paths — array of file paths
        // $event->pageCount — number of pages scanned
        // $event->outputFormat — 'jpeg' or 'pdf'
    }
}
```

## Usage (JavaScript)

```js
import {
  scan,
  Events,
} from "../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js";
import { On } from "#nativephp";

// Open scanner
await scan({ maxPages: 3, outputFormat: "jpeg", jpegQuality: 90 });

// Listen for results
On(Events.DocumentScanned, (payload) => {
  console.log("Scanned:", payload.paths, payload.pageCount);
});

On(Events.ScanCancelled, () => {
  console.log("Cancelled");
});

On(Events.ScanFailed, (payload) => {
  console.error("Failed:", payload.error);
});
```

## Events

| Event             | Payload                              | When                            |
| ----------------- | ------------------------------------ | ------------------------------- |
| `DocumentScanned` | `paths`, `pageCount`, `outputFormat` | Scanning completed successfully |
| `ScanCancelled`   | —                                    | User cancelled the scanner      |
| `ScanFailed`      | `error`                              | An error occurred               |

## Required Permissions

Declared automatically via `nativephp.json`. No manual configuration needed.

**Android:** `CAMERA` — ML Kit handles the scanner UI internally.

**iOS:** Camera access is requested at runtime by VisionKit automatically.

No API keys or internet required.

## Documentation

- [Installation](docs/installation.md) — requirements, setup steps, verification
- [Configuration](docs/configuration.md) — all config options explained
- [Usage with Livewire](docs/livewire.md) — Livewire components and event handling
- [Usage with JavaScript](docs/javascript.md) — Inertia Vue/React integration
- [API Reference](docs/api-reference.md) — events, DTOs, enums, validation, contracts

## Testing

```bash
composer test
composer analyse
```

## Requirements

- PHP 8.3+
- NativePHP Mobile v3+
- iOS 13+ / Android API 21+

## License

MIT
