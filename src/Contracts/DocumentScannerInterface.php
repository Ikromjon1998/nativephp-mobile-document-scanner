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
     * Combine image files into a single PDF.
     *
     * @param  array<int, string>  $paths
     * @return array<string, mixed>
     */
    public function imagesToPdf(array $paths, ?string $outputPath = null): array;

    /**
     * Extract page thumbnails from a PDF as JPEG images.
     *
     * @return array<string, mixed>
     */
    public function pdfToImages(string $pdfPath, ?int $quality = 80): array;
}
