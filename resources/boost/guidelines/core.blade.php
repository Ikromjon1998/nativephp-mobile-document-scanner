<document-scanner-guidelines>

# Document Scanner Plugin — AI Guidelines

## Facade

```php
use Ikromjon\DocumentScanner\Facades\DocumentScanner;
```

### Methods

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `scan($options)` | `ScanOptions\|array` | `array` | Open the native document scanner. Results delivered via events. |

### Scan Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `maxPages` | int | No | Max pages to scan (0 = unlimited, default from config) |
| `outputFormat` | OutputFormat\|string | No | `jpeg` or `pdf` (default from config) |
| `jpegQuality` | int | No | 1-100 (default from config, only for jpeg) |

### Type-Safe DTOs

```php
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;

DocumentScanner::scan(new ScanOptions(
    maxPages: 5,
    outputFormat: OutputFormat::Jpeg,
    jpegQuality: 85,
));
```

## Events

| Event | Payload | When |
|-------|---------|------|
| `DocumentScanned` | `paths`, `pageCount`, `outputFormat` | Scanning completed successfully |
| `ScanCancelled` | — | User cancelled the scanner |
| `ScanFailed` | `error` | An error occurred |

Events are dispatched to **all** contexts simultaneously. Listen in whichever fits your stack:

### Livewire

```php
#[OnNative(DocumentScanned::class)]
public function onScanned($data) { /* $data['paths'], $data['pageCount'] */ }

#[OnNative(ScanCancelled::class)]
public function onCancelled() { /* user cancelled */ }

#[OnNative(ScanFailed::class)]
public function onFailed($data) { /* $data['error'] */ }
```

### Laravel Listeners

```php
use Ikromjon\DocumentScanner\Events\DocumentScanned;

class HandleDocumentScanned
{
    public function handle(DocumentScanned $event): void
    {
        // $event->paths, $event->pageCount, $event->outputFormat
    }
}
```

### JavaScript (Inertia / Vue / React)

```js
import { scan, Events } from '../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js';
import { On } from '#nativephp';

await scan({ maxPages: 3, outputFormat: 'jpeg', jpegQuality: 90 });

On(Events.DocumentScanned, (payload) => {
    console.log('Scanned:', payload.paths, payload.pageCount);
});

On(Events.ScanCancelled, () => {
    console.log('User cancelled scanning');
});

On(Events.ScanFailed, (payload) => {
    console.error('Scan failed:', payload.error);
});
```

## Configuration

Publish with `php artisan vendor:publish --tag=document-scanner-config`.

| Key | Default | Description |
|-----|---------|-------------|
| `default_max_pages` | `0` | Default max pages (0 = unlimited) |
| `max_pages_limit` | `100` | Absolute cap on pages per scan |
| `default_output_format` | `jpeg` | Default output format |
| `default_jpeg_quality` | `90` | Default JPEG quality |
| `storage_directory` | `scanned-documents` | Storage subdirectory for scanned files |

Config is injected into the bridge call via `_config` key — both Android (Kotlin) and iOS (Swift) read applicable values at runtime.

## Common Patterns

- The scanner returns results asynchronously via events, not as a return value from `scan()`.
- Scanned files are stored in the app's documents directory under `storage_directory`.
- Clean up scanned files after uploading to avoid filling device storage.
- Use `maxPages: 1` for single-document capture (ID cards, receipts).
- Use `outputFormat: 'pdf'` when scanning multi-page documents.
- Camera permission is handled automatically by the native scanner UI.

## Required Permissions

Declared via `nativephp.json` — no manual setup needed.

**Android:** `CAMERA`. ML Kit Document Scanner handles the camera UI internally.
**iOS:** Camera access requested at runtime by VisionKit automatically.

No environment variables or API keys required.

</document-scanner-guidelines>
