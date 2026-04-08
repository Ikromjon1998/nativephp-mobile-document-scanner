<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Validation;

use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;

final class ScanValidator
{
    /**
     * @param  array<string, mixed>  $options
     *
     * @throws \InvalidArgumentException
     */
    public static function validate(array $options): void
    {
        if (isset($options['maxPages'])) {
            if ($options['maxPages'] < 0) {
                throw new \InvalidArgumentException('maxPages must be 0 (unlimited) or a positive integer.');
            }

            $maxPagesLimit = max(1, (int) self::configValue('max_pages_limit', 100));
            if ($options['maxPages'] > $maxPagesLimit) {
                throw new \InvalidArgumentException("maxPages must not exceed {$maxPagesLimit}.");
            }
        }

        if (isset($options['outputFormat']) && OutputFormat::tryFrom($options['outputFormat']) === null) {
            throw new \InvalidArgumentException('outputFormat must be "jpeg" or "pdf".');
        }

        if (isset($options['jpegQuality']) && ($options['jpegQuality'] < 1 || $options['jpegQuality'] > 100)) {
            throw new \InvalidArgumentException('jpegQuality must be between 1 and 100.');
        }

        if (isset($options['galleryImport']) && ! is_bool($options['galleryImport'])) {
            throw new \InvalidArgumentException('galleryImport must be a boolean.');
        }

        if (isset($options['scannerMode']) && ScannerMode::tryFrom($options['scannerMode']) === null) {
            throw new \InvalidArgumentException('scannerMode must be "base", "filter", or "full".');
        }
    }

    private static function configValue(string $key, mixed $default = null): mixed
    {
        if (function_exists('config')) {
            return config("document-scanner.{$key}", $default);
        }

        return $default;
    }
}
