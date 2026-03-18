<?php

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
}
