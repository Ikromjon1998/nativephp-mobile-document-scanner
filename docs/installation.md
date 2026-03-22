# Installation

## Requirements

- PHP 8.3 or higher
- Laravel 10 or higher
- NativePHP Mobile v3+
- iOS 13+ or Android API 21+

## Step 1: Install the Package

```bash
composer require ikromjon/nativephp-mobile-document-scanner
```

The service provider registers automatically via Laravel's package discovery.

## Step 2: Register the Native Plugin

```bash
php artisan native:plugin:register ikromjon/nativephp-mobile-document-scanner
```

This registers the native bridge functions defined in `nativephp.json` so the scanner can communicate with the native iOS/Android APIs.

## Step 3: Build Your App

The plugin requires a native build — it won't work in the browser or with `php artisan serve`.

```bash
# Android
php artisan native:run android

# iOS
php artisan native:run ios
```

## Optional: Publish Configuration

```bash
php artisan vendor:publish --tag=document-scanner-config
```

This creates `config/document-scanner.php` where you can customize defaults:

```php
return [
    'default_max_pages'     => 0,                  // 0 = unlimited
    'max_pages_limit'       => 100,                // absolute cap
    'default_output_format' => 'jpeg',             // 'jpeg' or 'pdf'
    'default_jpeg_quality'  => 90,                 // 1-100
    'storage_directory'     => 'scanned-documents', // where files are saved
];
```

## Verify Installation

You can verify the package is installed by checking:

```bash
php artisan about | grep -i scanner
```

Or test in a Tinker session:

```bash
php artisan tinker
>>> app(\Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface::class)
```

## Next Steps

- [Usage with Livewire](livewire.md) — build a scanner component
- [Usage with Inertia (Vue/React)](javascript.md) — use the JavaScript API
- [Configuration](configuration.md) — customize all options
