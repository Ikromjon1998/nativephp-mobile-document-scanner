<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Facades;

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> scan(ScanOptions|array<string, mixed> $options = [])
 * @method static array<string, mixed> imagesToPdf(array<int, string> $paths, ?string $outputPath = null)
 * @method static array<string, mixed> pdfToImages(string $pdfPath, ?int $quality = 80)
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
