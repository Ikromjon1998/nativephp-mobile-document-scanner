# NativePHP Mobile Document Scanner

[![Tests](https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/Ikromjon1998/nativephp-mobile-document-scanner/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/github/license/Ikromjon1998/nativephp-mobile-document-scanner)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-8.3%2B-8892BF)](composer.json)

Scan documents with automatic edge detection, perspective correction, and cropping in your NativePHP Mobile app — powered by native platform APIs.

## Quick Start

```bash
composer require ikromjon/nativephp-mobile-document-scanner
php artisan native:plugin:register ikromjon/nativephp-mobile-document-scanner
php artisan native:run android  # or ios
```

```php
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Native\Mobile\Attributes\OnNative;

// Open the scanner
DocumentScanner::scan();

// Handle the result
#[OnNative(DocumentScanned::class)]
public function onScanned($data)
{
    $paths = $data['paths'];           // ['/path/scan_0.jpg', ...]
    $pageCount = $data['pageCount'];   // 2
}
```

That's it. The scanner opens, the user scans, and you get the file paths back via events. See below for full options and JavaScript usage.

## How It Works

| Platform    | Native API                                   | Features                                                                |
| ----------- | -------------------------------------------- | ----------------------------------------------------------------------- |
| **iOS**     | VisionKit (`VNDocumentCameraViewController`) | Auto edge detection, perspective correction, shadow removal, multi-page |
| **Android** | Google ML Kit Document Scanner               | Auto edge detection, cropping, rotation, multi-page, gallery import     |

No external API keys or internet required. Camera permission is handled automatically.

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

> **Note:** The scanner won't work with `php artisan serve`. You need a native build on a real device. If you call `scan()` without a native build, you'll see a warning in your Laravel log.

You can check at runtime whether the native bridge is available:

```php
if (DocumentScanner::isAvailable()) {
    DocumentScanner::scan();
}
```

## Configuration

Optionally publish the config file:

```bash
php artisan vendor:publish --tag=document-scanner-config
```

| Key                     | Default             | Description                                            |
| ----------------------- | ------------------- | ------------------------------------------------------ |
| `default_max_pages`     | `0`                 | Default max pages per scan (0 = unlimited)             |
| `max_pages_limit`       | `100`               | Absolute cap on pages per scan                         |
| `default_output_format` | `jpeg`              | Default output format (`jpeg` or `pdf`)                |
| `default_jpeg_quality`  | `90`                | Default JPEG compression quality (1-100)               |
| `storage_directory`     | `scanned-documents` | Subdirectory for scanned files                         |
| `default_gallery_import`| `false`             | Allow gallery import (Android only)                    |
| `default_scanner_mode`  | `full`              | Scanner mode: `base`, `filter`, `full` (Android only)  |

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
    outputFormat: OutputFormat::Pdf,
));
```

### Scan Parameters

| Parameter       | Type                 | Platform     | Description                               |
| --------------- | -------------------- | ------------ | ----------------------------------------- |
| `maxPages`      | int                  | Both         | Max pages to scan (0 = unlimited)         |
| `outputFormat`  | OutputFormat\|string | Both         | `jpeg` or `pdf`                           |
| `jpegQuality`   | int                  | Both         | JPEG quality 1-100 (only for jpeg output) |
| `galleryImport` | bool                 | Android only | Allow importing from device gallery       |
| `scannerMode`   | ScannerMode\|string  | Android only | `base`, `filter`, or `full`               |

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
| `ScanCancelled`   | ---                                  | User cancelled the scanner      |
| `ScanFailed`      | `error`                              | An error occurred               |

## Documentation

- [Installation](docs/installation.md) --- requirements, setup steps, verification
- [Configuration](docs/configuration.md) --- all config options explained
- [Usage with Livewire](docs/livewire.md) --- Livewire components and event handling
- [Usage with JavaScript](docs/javascript.md) --- Inertia Vue/React integration
- [API Reference](docs/api-reference.md) --- events, DTOs, enums, validation, contracts

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
