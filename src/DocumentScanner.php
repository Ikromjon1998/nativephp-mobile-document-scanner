<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner;

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
use Ikromjon\DocumentScanner\Enums\ScannerMode;
use Ikromjon\DocumentScanner\Validation\ScanValidator;

class DocumentScanner implements DocumentScannerInterface
{
    /**
     * Open the document scanner.
     *
     * @param  ScanOptions|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function scan(ScanOptions|array $options = []): array
    {
        $data = $options instanceof ScanOptions
            ? $options->toArray()
            : $this->normalizeOptions($options);

        return $this->call('DocumentScanner.Scan', $data);
    }

    /**
     * Combine image files into a single PDF.
     *
     * @param  array<int, mixed>  $paths
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function imagesToPdf(array $paths, ?string $outputPath = null): array
    {
        if ($paths === []) {
            throw new \InvalidArgumentException('paths must be a non-empty array.');
        }

        foreach ($paths as $path) {
            if (! is_string($path)) {
                throw new \InvalidArgumentException('Each path must be a string.');
            }
        }

        $data = ['paths' => array_values($paths)];

        if ($outputPath !== null) {
            if (trim($outputPath) === '') {
                throw new \InvalidArgumentException('outputPath must be a non-empty string when provided.');
            }
            $data['outputPath'] = $outputPath;
        }

        return $this->call('DocumentScanner.ImagesToPdf', $data);
    }

    /**
     * Extract page thumbnails from a PDF as JPEG images.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function pdfToImages(string $pdfPath, ?int $quality = 80): array
    {
        if ($pdfPath === '') {
            throw new \InvalidArgumentException('pdfPath must be a non-empty string.');
        }

        if ($quality !== null && ($quality < 1 || $quality > 100)) {
            throw new \InvalidArgumentException('quality must be between 1 and 100.');
        }

        $data = ['pdfPath' => $pdfPath];

        if ($quality !== null) {
            $data['quality'] = $quality;
        }

        return $this->call('DocumentScanner.PdfToImages', $data);
    }

    /**
     * Normalize a raw options array, converting enum values to strings and validating.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeOptions(array $options): array
    {
        if (isset($options['outputFormat']) && $options['outputFormat'] instanceof OutputFormat) {
            $options['outputFormat'] = $options['outputFormat']->value;
        }

        if (isset($options['scannerMode']) && $options['scannerMode'] instanceof ScannerMode) {
            $options['scannerMode'] = $options['scannerMode']->value;
        }

        ScanValidator::validate($options);

        return $options;
    }

    /**
     * Build the config values to pass to the native layer.
     *
     * @return array<string, mixed>
     */
    protected function nativeConfig(): array
    {
        return [
            'default_max_pages' => $this->configValue('default_max_pages', 0),
            'default_output_format' => $this->configValue('default_output_format', 'jpeg'),
            'default_jpeg_quality' => $this->configValue('default_jpeg_quality', 90),
            'max_pages_limit' => $this->configValue('max_pages_limit', 100),
            'storage_directory' => $this->configValue('storage_directory', 'scanned-documents'),
            'default_gallery_import' => $this->configValue('default_gallery_import', false),
            'default_scanner_mode' => $this->configValue('default_scanner_mode', 'full'),
        ];
    }

    /**
     * Read a config value with a fallback for environments where config() is unavailable.
     */
    protected function configValue(string $key, mixed $default = null): mixed
    {
        if (function_exists('config')) {
            return config("document-scanner.{$key}", $default);
        }

        return $default;
    }

    /**
     * Make a bridge call to the native layer.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function call(string $function, array $data = []): array
    {
        if (! function_exists('nativephp_call')) {
            if (function_exists('logger')) {
                logger()->warning(
                    'DocumentScanner: nativephp_call() is not available. '
                    .'The document scanner requires a NativePHP native build. '
                    .'Run: php artisan native:run android|ios',
                );
            }

            return [];
        }

        $data['_config'] = $this->nativeConfig();

        $payload = json_encode($data);

        if ($payload === false) {
            if (function_exists('logger')) {
                logger()->warning(
                    'DocumentScanner: failed to encode bridge payload: '.json_last_error_msg(),
                );
            }

            return [];
        }

        $result = nativephp_call($function, $payload);

        if (! $result) {
            return [];
        }

        $decoded = json_decode($result, true) ?? [];

        if (isset($decoded['error']) && is_string($decoded['error'])) {
            throw new \RuntimeException($decoded['error']);
        }

        return $decoded;
    }
}
