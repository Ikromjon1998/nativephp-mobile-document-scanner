# API Reference

## DocumentScanner Facade

```php
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
```

### `scan(ScanOptions|array $options = []): array`

Opens the native document scanner UI. Returns immediately — results arrive via events.

```php
// With defaults
DocumentScanner::scan();

// With array options
DocumentScanner::scan([
    'maxPages' => 3,
    'outputFormat' => 'jpeg',
    'jpegQuality' => 85,
]);

// With ScanOptions DTO
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;

DocumentScanner::scan(new ScanOptions(
    maxPages: 5,
    outputFormat: OutputFormat::Pdf,
));
```

## ScanOptions DTO

```php
use Ikromjon\DocumentScanner\Data\ScanOptions;
```

Type-safe alternative to passing raw arrays.

### Constructor

```php
new ScanOptions(
    maxPages: int = 0,
    outputFormat: OutputFormat|string = OutputFormat::Jpeg,
    jpegQuality: int = 90,
    galleryImport: bool = false,
    scannerMode: ScannerMode|string = ScannerMode::Full,
)
```

| Property       | Type                   | Default              | Description               |
| -------------- | ---------------------- | -------------------- | ------------------------- |
| `maxPages`     | `int`                  | `0`                  | Max pages (0 = unlimited) |
| `outputFormat` | `OutputFormat\|string` | `OutputFormat::Jpeg` | `'jpeg'` or `'pdf'`       |
| `jpegQuality`  | `int`                  | `90`                 | JPEG quality 1-100        |
| `galleryImport`| `bool`                 | `false`              | Allow gallery import (Android only) |
| `scannerMode`  | `ScannerMode\|string`  | `ScannerMode::Full`  | Scanner mode (Android only)         |

### Methods

- `toArray(): array` — converts to associative array, runs validation

## OutputFormat Enum

```php
use Ikromjon\DocumentScanner\Enums\OutputFormat;
```

| Case   | Value    |
| ------ | -------- |
| `Jpeg` | `'jpeg'` |
| `Pdf`  | `'pdf'`  |

## ScannerMode Enum

```php
use Ikromjon\DocumentScanner\Enums\ScannerMode;
```

| Case     | Value      | Description                          |
| -------- | ---------- | ------------------------------------ |
| `Base`   | `'base'`   | Fast, minimal processing             |
| `Filter` | `'filter'` | Adds grayscale/color filter options  |
| `Full`   | `'full'`   | Full ML-enhanced cleaning (default)  |

**Android only** — iOS VisionKit always applies full processing.

## Events

All events use Laravel's `Dispatchable` and `SerializesModels` traits.

### DocumentScanned

Fired when scanning completes successfully.

```php
use Ikromjon\DocumentScanner\Events\DocumentScanned;
```

| Property        | Type     | Description                      |
| --------------- | -------- | -------------------------------- |
| `$paths`        | `array`  | File paths to scanned documents  |
| `$pageCount`    | `int`    | Number of pages scanned          |
| `$outputFormat` | `string` | Format used: `'jpeg'` or `'pdf'` |

**Livewire:**

```php
#[OnNative(DocumentScanned::class)]
public function onScanned($data)
{
    $data['paths'];        // ['/path/scan_0.jpg', '/path/scan_1.jpg']
    $data['pageCount'];    // 2
    $data['outputFormat']; // 'jpeg'
}
```

**Laravel event listener:**

```php
public function handle(DocumentScanned $event): void
{
    $event->paths;        // array of file paths
    $event->pageCount;    // number of pages
    $event->outputFormat; // 'jpeg' or 'pdf'
}
```

### ScanCancelled

Fired when the user dismisses the scanner without scanning.

```php
use Ikromjon\DocumentScanner\Events\ScanCancelled;
```

No payload.

### ScanFailed

Fired when an error occurs during scanning.

```php
use Ikromjon\DocumentScanner\Events\ScanFailed;
```

| Property | Type     | Description   |
| -------- | -------- | ------------- |
| `$error` | `string` | Error message |

## Validation

```php
use Ikromjon\DocumentScanner\Validation\ScanValidator;
```

### `ScanValidator::validate(array $options): void`

Validates scan options. Throws `InvalidArgumentException` on failure.

**Rules:**

| Option         | Rule                                                                                |
| -------------- | ----------------------------------------------------------------------------------- |
| `maxPages`     | Must be `0` (unlimited) or positive integer, cannot exceed `max_pages_limit` config |
| `outputFormat` | Must be `'jpeg'` or `'pdf'`                                                         |
| `jpegQuality`  | Must be between `1` and `100`                                                       |
| `galleryImport`| Must be a boolean                                                                   |
| `scannerMode`  | Must be `'base'`, `'filter'`, or `'full'`                                           |

Validation runs automatically when calling `DocumentScanner::scan()` or `ScanOptions::toArray()`.

## Contract

```php
use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
```

Interface for the scanner service. Useful for dependency injection and testing:

```php
public function scan(ScanOptions|array $options = []): array;
```

Bind your own implementation:

```php
$this->app->singleton(DocumentScannerInterface::class, MyCustomScanner::class);
```
