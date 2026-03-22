# Configuration

## Publishing the Config File

```bash
php artisan vendor:publish --tag=document-scanner-config
```

This creates `config/document-scanner.php`:

```php
return [
    'default_max_pages'     => 0,
    'max_pages_limit'       => 100,
    'default_output_format' => 'jpeg',
    'default_jpeg_quality'  => 90,
    'storage_directory'     => 'scanned-documents',
];
```

## Options Reference

### `default_max_pages`

|             |                          |
| ----------- | ------------------------ |
| **Type**    | `int`                    |
| **Default** | `0`                      |
| **Range**   | `0` to `max_pages_limit` |

Maximum number of pages per scan session. Set to `0` for unlimited. This is the default used when `maxPages` is not passed to `scan()`.

### `max_pages_limit`

|             |       |
| ----------- | ----- |
| **Type**    | `int` |
| **Default** | `100` |
| **Minimum** | `1`   |

Absolute cap on pages per scan. Prevents accidental memory issues from scanning too many pages. The `maxPages` parameter cannot exceed this value.

### `default_output_format`

|             |                   |
| ----------- | ----------------- |
| **Type**    | `string`          |
| **Default** | `'jpeg'`          |
| **Options** | `'jpeg'`, `'pdf'` |

Default output format. When set to `jpeg`, each scanned page is saved as a separate JPEG file. When set to `pdf`, all pages are combined into a single PDF.

### `default_jpeg_quality`

|             |              |
| ----------- | ------------ |
| **Type**    | `int`        |
| **Default** | `90`         |
| **Range**   | `1` to `100` |

JPEG compression quality. Higher values produce better quality but larger files. Only applies when output format is `jpeg`.

### `storage_directory`

|             |                       |
| ----------- | --------------------- |
| **Type**    | `string`              |
| **Default** | `'scanned-documents'` |

Subdirectory within the app's storage where scanned files are saved. On Android this is relative to `context.filesDir`, on iOS to the app's documents directory.

## Overriding Per Scan

Config values are defaults. You can override them on each scan call:

```php
// Uses config defaults
DocumentScanner::scan();

// Override specific options for this scan
DocumentScanner::scan([
    'maxPages' => 1,
    'outputFormat' => 'pdf',
]);
```

## Runtime Access

```php
config('document-scanner.default_max_pages');      // 0
config('document-scanner.max_pages_limit');         // 100
config('document-scanner.default_output_format');   // 'jpeg'
config('document-scanner.default_jpeg_quality');    // 90
config('document-scanner.storage_directory');        // 'scanned-documents'
```

## Environment-Specific Config

You can use environment variables in your config file:

```php
// config/document-scanner.php
return [
    'default_jpeg_quality' => env('SCANNER_JPEG_QUALITY', 90),
    'max_pages_limit'      => env('SCANNER_MAX_PAGES', 100),
    'storage_directory'    => env('SCANNER_STORAGE_DIR', 'scanned-documents'),
];
```
