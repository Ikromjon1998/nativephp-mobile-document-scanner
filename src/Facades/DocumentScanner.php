<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Facades;

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> scan(ScanOptions|array<string, mixed> $options = [])
 * @method static bool isAvailable()
 *
 * @see \Ikromjon\DocumentScanner\DocumentScanner
 */
class DocumentScanner extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DocumentScannerInterface::class;
    }
}
