<?php

namespace Ikromjon\DocumentScanner\Validation;

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

        if (isset($options['outputFormat'])) {
            $valid = ['jpeg', 'pdf'];
            if (! in_array($options['outputFormat'], $valid, true)) {
                throw new \InvalidArgumentException('outputFormat must be "jpeg" or "pdf".');
            }
        }

        if (isset($options['jpegQuality'])) {
            if ($options['jpegQuality'] < 1 || $options['jpegQuality'] > 100) {
                throw new \InvalidArgumentException('jpegQuality must be between 1 and 100.');
            }
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
