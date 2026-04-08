<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Contracts;

use Ikromjon\DocumentScanner\Data\ScanOptions;

interface DocumentScannerInterface
{
    /**
     * Open the document scanner.
     *
     * @param  ScanOptions|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function scan(ScanOptions|array $options = []): array;

    /**
     * Check whether the native bridge is available.
     *
     * Returns false when running outside a NativePHP native build
     * (e.g. php artisan serve, browser, or tests without stubs).
     */
    public function isAvailable(): bool;
}
