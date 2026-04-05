<?php

declare(strict_types=1);

use Ikromjon\DocumentScanner\Contracts\DocumentScannerInterface;
use Ikromjon\DocumentScanner\DocumentScanner;

describe('config defaults', function (): void {
    it('has all expected config keys with defaults', function (): void {
        expect(config('document-scanner.default_max_pages'))->toBe(0)
            ->and(config('document-scanner.max_pages_limit'))->toBe(100)
            ->and(config('document-scanner.default_output_format'))->toBe('jpeg')
            ->and(config('document-scanner.default_jpeg_quality'))->toBe(90)
            ->and(config('document-scanner.storage_directory'))->toBe('scanned-documents')
            ->and(config('document-scanner.default_gallery_import'))->toBeFalse();
    });
});

describe('config overrides', function (): void {
    it('allows overriding default_max_pages', function (): void {
        config(['document-scanner.default_max_pages' => 5]);

        expect(config('document-scanner.default_max_pages'))->toBe(5);
    });

    it('allows overriding max_pages_limit', function (): void {
        config(['document-scanner.max_pages_limit' => 50]);

        expect(config('document-scanner.max_pages_limit'))->toBe(50);
    });

    it('allows overriding default_output_format', function (): void {
        config(['document-scanner.default_output_format' => 'pdf']);

        expect(config('document-scanner.default_output_format'))->toBe('pdf');
    });

    it('allows overriding default_jpeg_quality', function (): void {
        config(['document-scanner.default_jpeg_quality' => 75]);

        expect(config('document-scanner.default_jpeg_quality'))->toBe(75);
    });

    it('allows overriding storage_directory', function (): void {
        config(['document-scanner.storage_directory' => 'custom-scans']);

        expect(config('document-scanner.storage_directory'))->toBe('custom-scans');
    });

    it('allows overriding default_gallery_import', function (): void {
        config(['document-scanner.default_gallery_import' => true]);

        expect(config('document-scanner.default_gallery_import'))->toBeTrue();
    });
});

describe('config flows to bridge', function (): void {
    it('injects overridden default_max_pages into _config', function (): void {
        config(['document-scanner.default_max_pages' => 10]);

        $capturedData = null;
        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        (new DocumentScanner)->scan();

        expect($capturedData['_config']['default_max_pages'])->toBe(10);
    });

    it('injects overridden default_output_format into _config', function (): void {
        config(['document-scanner.default_output_format' => 'pdf']);

        $capturedData = null;
        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        (new DocumentScanner)->scan();

        expect($capturedData['_config']['default_output_format'])->toBe('pdf');
    });

    it('injects overridden default_jpeg_quality into _config', function (): void {
        config(['document-scanner.default_jpeg_quality' => 75]);

        $capturedData = null;
        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        (new DocumentScanner)->scan();

        expect($capturedData['_config']['default_jpeg_quality'])->toBe(75);
    });

    it('injects overridden storage_directory into _config', function (): void {
        config(['document-scanner.storage_directory' => 'custom-dir']);

        $capturedData = null;
        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        (new DocumentScanner)->scan();

        expect($capturedData['_config']['storage_directory'])->toBe('custom-dir');
    });

    it('injects overridden default_gallery_import into _config', function (): void {
        config(['document-scanner.default_gallery_import' => true]);

        $capturedData = null;
        stubNativephpCall(function (string $function, string $data) use (&$capturedData) {
            $capturedData = json_decode($data, true);

            return json_encode(['success' => true]);
        });

        (new DocumentScanner)->scan();

        expect($capturedData['_config']['default_gallery_import'])->toBeTrue();
    });
});

describe('interface contract', function (): void {
    it('implements DocumentScannerInterface', function (): void {
        $scanner = new DocumentScanner;

        expect($scanner)->toBeInstanceOf(DocumentScannerInterface::class);
    });

    it('has the scan method', function (): void {
        $reflection = new ReflectionClass(DocumentScanner::class);

        expect($reflection->hasMethod('scan'))->toBeTrue();
    });
});
