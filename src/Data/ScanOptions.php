<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Data;

use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;
use Ikromjon\DocumentScanner\Validation\ScanValidator;

final readonly class ScanOptions
{
    public function __construct(
        public int $maxPages = 0,
        public OutputFormat|string $outputFormat = OutputFormat::Jpeg,
        public int $jpegQuality = 90,
        public bool $galleryImport = false,
        public ScannerMode|string $scannerMode = ScannerMode::Full,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->maxPages > 0) {
            $data['maxPages'] = $this->maxPages;
        }

        $outputFormat = $this->outputFormat instanceof OutputFormat
            ? $this->outputFormat->value
            : $this->outputFormat;
        if ($outputFormat !== OutputFormat::Jpeg->value) {
            $data['outputFormat'] = $outputFormat;
        }

        if ($this->jpegQuality !== 90) {
            $data['jpegQuality'] = $this->jpegQuality;
        }

        if ($this->galleryImport) {
            $data['galleryImport'] = true;
        }

        $scannerMode = $this->scannerMode instanceof ScannerMode
            ? $this->scannerMode->value
            : $this->scannerMode;
        if ($scannerMode !== ScannerMode::Full->value) {
            $data['scannerMode'] = $scannerMode;
        }

        ScanValidator::validate($data);

        return $data;
    }
}
