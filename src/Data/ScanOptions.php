<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Data;

use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Validation\ScanValidator;

final readonly class ScanOptions
{
    public function __construct(
        public int $maxPages = 0,
        public OutputFormat|string $outputFormat = OutputFormat::Jpeg,
        public int $jpegQuality = 90,
        public bool $galleryImport = false,
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

        $data['outputFormat'] = $this->outputFormat instanceof OutputFormat
            ? $this->outputFormat->value
            : $this->outputFormat;

        $data['jpegQuality'] = $this->jpegQuality;

        if ($this->galleryImport) {
            $data['galleryImport'] = true;
        }

        ScanValidator::validate($data);

        return $data;
    }
}
