<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner;

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Ikromjon\DocumentScanner\Data\ScanOptions;
use Ikromjon\DocumentScanner\Enums\OutputFormat;
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
            return [];
        }

        $data['_config'] = $this->nativeConfig();

        $result = nativephp_call($function, json_encode($data));

        if (! $result) {
            return [];
        }

        return json_decode($result, true) ?? [];
    }
}
